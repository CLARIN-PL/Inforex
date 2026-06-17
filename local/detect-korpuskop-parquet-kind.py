#!/usr/bin/env python3

import os
import subprocess
import sys
import tempfile

try:
    import pyarrow.parquet as pq
except ModuleNotFoundError:
    print(
        "Brak zależności pyarrow. Autodetekcja wariantu eksportu Inforex wymaga środowiska Python z pyarrow.",
        file=sys.stderr,
    )
    raise SystemExit(3)


DOCUMENT_FIELDS = {
    "lemma_tokens",
    "surface_tokens",
    "pos_tokens",
    "sentence_token_lens",
}

DIALOG_FIELDS = {
    "dialog_id",
    "participants",
    "turns",
    "tokens",
}


def open_schema(path):
    if path.lower().endswith(".parquet"):
        return pq.read_schema(path)

    with tempfile.NamedTemporaryFile(suffix=".parquet") as handle:
        subprocess.check_call(["zstd", "-d", "-c", path], stdout=handle)
        handle.flush()
        return pq.read_schema(handle.name)


def detect_kind(path):
    schema = open_schema(path)
    names = set(schema.names)

    if DIALOG_FIELDS.issubset(names):
        return "dialog"
    if DOCUMENT_FIELDS.issubset(names):
        return "document"

    raise RuntimeError(
        "Nie rozpoznano wariantu eksportu Inforex. Oczekiwano schematu dokumentowego albo dialogowego."
    )


def main(argv):
    if len(argv) != 2:
        print("Usage: detect-korpuskop-parquet-kind.py <input.parquet.zst>", file=sys.stderr)
        return 2

    input_path = argv[1]
    if not os.path.isfile(input_path):
        print(f"Brak pliku: {input_path}", file=sys.stderr)
        return 2

    try:
        print(detect_kind(input_path))
        return 0
    except Exception as exc:
        print(str(exc), file=sys.stderr)
        return 1


if __name__ == "__main__":
    raise SystemExit(main(sys.argv))
