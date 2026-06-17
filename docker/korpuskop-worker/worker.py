#!/usr/bin/env python3
import json
import os
import subprocess
import threading
import time
import uuid
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from urllib.parse import parse_qs, urlparse


JOBS = {}
JOBS_LOCK = threading.Lock()


def make_job_state(command, cwd, progress_file, env):
    return {
        "job_id": str(uuid.uuid4()),
        "command": command,
        "cwd": cwd,
        "progress_file": progress_file,
        "env": env,
        "running": True,
        "exit_code": None,
        "stdout": "",
        "stderr_lines": [],
        "events": [],
        "last_seq": 0,
        "started_at": time.time(),
        "finished_at": None,
    }


def append_event(job, payload):
    with JOBS_LOCK:
        job["last_seq"] += 1
        job["events"].append({
            "seq": job["last_seq"],
            "payload": payload,
        })


def append_stderr_line(job, line):
    with JOBS_LOCK:
        job["stderr_lines"].append(line)


def append_stdout(job, chunk):
    with JOBS_LOCK:
        job["stdout"] += chunk


def mark_done(job, exit_code):
    with JOBS_LOCK:
        job["running"] = False
        job["exit_code"] = int(exit_code)
        job["finished_at"] = time.time()


def read_stdout(pipe, job):
    try:
        for line in iter(pipe.readline, ""):
            if not line:
                break
            append_stdout(job, line)
    finally:
        pipe.close()


def read_stderr(pipe, job):
    try:
        for line in iter(pipe.readline, ""):
            if not line:
                break
            stripped = line.strip()
            if not stripped:
                continue
            try:
                decoded = json.loads(stripped)
                if isinstance(decoded, dict):
                    append_event(job, decoded)
                else:
                    append_stderr_line(job, stripped)
            except Exception:
                append_stderr_line(job, stripped)
    finally:
        pipe.close()


def run_job(job):
    env = os.environ.copy()
    if isinstance(job.get("env"), dict):
        for key, value in job["env"].items():
            env[str(key)] = str(value)
    process = subprocess.Popen(
        job["command"],
        cwd=job["cwd"],
        env=env,
        stdin=subprocess.DEVNULL,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        text=True,
        bufsize=1,
    )
    stdout_thread = threading.Thread(target=read_stdout, args=(process.stdout, job), daemon=True)
    stderr_thread = threading.Thread(target=read_stderr, args=(process.stderr, job), daemon=True)
    stdout_thread.start()
    stderr_thread.start()
    exit_code = process.wait()
    stdout_thread.join(timeout=1)
    stderr_thread.join(timeout=1)
    mark_done(job, exit_code)


class Handler(BaseHTTPRequestHandler):
    server_version = "KorpuskopWorker/1.0"

    def log_message(self, fmt, *args):
        return

    def send_json(self, status, payload):
        body = json.dumps(payload).encode("utf-8")
        self.send_response(status)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)

    def read_json(self):
        length = int(self.headers.get("Content-Length", "0"))
        if length <= 0:
            return {}
        raw = self.rfile.read(length)
        return json.loads(raw.decode("utf-8"))

    def do_POST(self):
        if self.path != "/run":
            self.send_json(404, {"error": "not_found"})
            return

        try:
            payload = self.read_json()
            command = payload.get("command") or []
            cwd = payload.get("cwd") or "/opt/korpuskop"
            progress_file = payload.get("progress_file") or ""
            env = payload.get("env") or {}
            if not isinstance(command, list) or not command:
                self.send_json(400, {"error": "invalid_command"})
                return
            if not isinstance(env, dict):
                self.send_json(400, {"error": "invalid_env"})
                return

            job = make_job_state(command, cwd, progress_file, env)
            with JOBS_LOCK:
                JOBS[job["job_id"]] = job

            thread = threading.Thread(target=run_job, args=(job,), daemon=True)
            thread.start()
            self.send_json(200, {
                "job_id": job["job_id"],
                "progress_file": progress_file,
            })
        except Exception as exc:
            self.send_json(500, {"error": "start_failed", "message": str(exc)})

    def do_GET(self):
        parsed = urlparse(self.path)
        if not parsed.path.startswith("/status/"):
            self.send_json(404, {"error": "not_found"})
            return

        job_id = parsed.path.split("/")[-1]
        with JOBS_LOCK:
            job = JOBS.get(job_id)
            if not job:
                self.send_json(404, {"error": "job_not_found"})
                return

            query = parse_qs(parsed.query)
            after_seq = int(query.get("after_seq", ["0"])[0] or 0)
            events = [event for event in job["events"] if int(event["seq"]) > after_seq]
            payload = {
                "job_id": job["job_id"],
                "running": job["running"],
                "exit_code": job["exit_code"],
                "stdout": job["stdout"],
                "stderr_lines": list(job["stderr_lines"]),
                "progress_file": job["progress_file"],
                "last_seq": job["last_seq"],
                "events": events,
            }

        self.send_json(200, payload)


if __name__ == "__main__":
    os.makedirs("/opt/korpuskop/var/output", exist_ok=True)
    os.makedirs("/opt/korpuskop/var/progress", exist_ok=True)
    server = ThreadingHTTPServer(("0.0.0.0", 8090), Handler)
    server.serve_forever()
