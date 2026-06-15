#!/usr/bin/env python3

import json
import os
import subprocess
import sys
import tempfile

import pyarrow.parquet as pq


def collect_files(path):
    if os.path.isfile(path):
        return [path]

    result = []
    for root, _, files in os.walk(path):
        for filename in files:
            lower = filename.lower()
            if lower.endswith(".parquet") or lower.endswith(".parquet.zst"):
                result.append(os.path.join(root, filename))
    result.sort()
    return result


def open_parquet(path):
    if path.lower().endswith(".parquet"):
        return pq.read_table(path)

    with tempfile.NamedTemporaryFile(suffix=".parquet") as handle:
        subprocess.check_call(["zstd", "-d", "-c", path], stdout=handle)
        handle.flush()
        return pq.read_table(handle.name)


def validate_json_field(value, label, errors):
    if value in (None, ""):
        return
    try:
        json.loads(value)
    except Exception:
        errors.append(f"invalid {label}")


def validate_file(path):
    try:
        rows = open_parquet(path).to_pylist()
    except Exception as exc:
        return [f"cannot read parquet: {exc}"], {"dialogs": 0, "turns": 0, "tokens": 0, "annotations": 0, "relations": 0}

    errors = []
    stats = {"dialogs": 0, "turns": 0, "tokens": 0, "annotations": 0, "relations": 0}

    for row in rows:
        dialog_id = row.get("dialog_id") or "<missing>"
        subtitle = row.get("subtitle") or ""
        participants = row.get("participants") or []
        turns = row.get("turns") or []
        tokens = row.get("tokens") or []
        annotations = row.get("annotations") or []
        relations = row.get("relations") or []

        stats["dialogs"] += 1
        stats["turns"] += len(turns)
        stats["tokens"] += len(tokens)
        stats["annotations"] += len(annotations)
        stats["relations"] += len(relations)

        validate_json_field(row.get("metadata_json"), "metadata_json", errors)
        validate_json_field(row.get("metadata_types_json"), "metadata_types_json", errors)

        turn_by_id = {}
        turn_numbers = []
        for turn in turns:
            turn_id = turn.get("turn_id")
            if not turn_id:
                errors.append(f"{dialog_id}: turn without turn_id")
                continue
            turn_by_id[turn_id] = turn
            turn_numbers.append(turn.get("sequence_no"))
            if turn.get("kind") not in ("utterance", "stage_direction", "document"):
                errors.append(f"{dialog_id}: turn {turn_id} invalid kind")

        if turn_numbers and turn_numbers != list(range(1, len(turn_numbers) + 1)):
            errors.append(f"{dialog_id}: non-contiguous turn sequence numbers")

        annotation_ids = set()
        token_sequences = []

        for token in tokens:
            token_id = token.get("token_id")
            turn_id = token.get("turn_id")
            segment_type = token.get("segment_type")
            local_start = token.get("local_start")
            local_stop = token.get("local_stop")
            orth = token.get("orth") or ""
            token_sequences.append(token.get("sequence_no"))

            if None in (local_start, local_stop) or local_start > local_stop:
                errors.append(f"{dialog_id}: token {token_id} invalid local offsets")
                continue

            if segment_type == "subtitle":
                base = subtitle
            elif segment_type == "participant":
                base = None
                for participant in participants:
                    if local_stop <= len(participant) and participant[local_start:local_stop] == orth:
                        base = participant
                        break
                if base is None:
                    errors.append(f"{dialog_id}: token {token_id} participant mismatch")
                    continue
            elif segment_type in ("author", "content"):
                if turn_id not in turn_by_id:
                    errors.append(f"{dialog_id}: token {token_id} missing turn {turn_id}")
                    continue
                turn = turn_by_id[turn_id]
                base = (turn.get("raw_author") or turn.get("speaker") or "") if segment_type == "author" else (turn.get("text") or "")
            else:
                errors.append(f"{dialog_id}: token {token_id} unknown segment_type {segment_type}")
                continue

            if local_stop > len(base) or base[local_start:local_stop] != orth:
                errors.append(f"{dialog_id}: token {token_id} {segment_type} mismatch")

        if token_sequences and token_sequences != sorted(token_sequences):
            errors.append(f"{dialog_id}: token sequence numbers are not sorted")

        for annotation in annotations:
            annotation_id = annotation.get("obj_id")
            annotation_ids.add(annotation_id)
            turn_id = annotation.get("turn_id")
            segment_type = annotation.get("segment_type")
            local_start = annotation.get("local_start")
            local_stop = annotation.get("local_stop")
            text = annotation.get("text") or ""

            if None in (local_start, local_stop) or local_start > local_stop:
                errors.append(f"{dialog_id}: annotation {annotation_id} invalid local offsets")
                continue

            if segment_type not in ("author", "content"):
                errors.append(f"{dialog_id}: annotation {annotation_id} unknown segment_type {segment_type}")
                continue

            if turn_id not in turn_by_id:
                errors.append(f"{dialog_id}: annotation {annotation_id} missing turn {turn_id}")
                continue

            turn = turn_by_id[turn_id]
            base = (turn.get("raw_author") or turn.get("speaker") or "") if segment_type == "author" else (turn.get("text") or "")
            if local_stop > len(base) or base[local_start:local_stop] != text:
                errors.append(f"{dialog_id}: annotation {annotation_id} {segment_type} mismatch")

        for relation in relations:
            relation_id = relation.get("obj_id")
            if relation.get("source_obj_id") not in annotation_ids:
                errors.append(f"{dialog_id}: relation {relation_id} missing source annotation")
            if relation.get("target_obj_id") not in annotation_ids:
                errors.append(f"{dialog_id}: relation {relation_id} missing target annotation")

    return errors, stats


def main():
    target = sys.argv[1] if len(sys.argv) > 1 else "test-files"
    files = collect_files(target)
    if not files:
        print(f"No parquet files found in: {target}", file=sys.stderr)
        return 1

    summary = {"files": 0, "errors": 0, "dialogs": 0, "turns": 0, "tokens": 0, "annotations": 0, "relations": 0}

    for path in files:
        summary["files"] += 1
        errors, stats = validate_file(path)
        for key, value in stats.items():
            summary[key] += value

        if not errors:
            print(f"OK {path}")
            continue

        summary["errors"] += len(errors)
        print(f"FAIL {path}")
        for error in errors[:20]:
            print(f"- {error}")
        if len(errors) > 20:
            print(f"- ... {len(errors) - 20} more")

    print("SUMMARY " + json.dumps(summary, ensure_ascii=False, sort_keys=True))
    return 0 if summary["errors"] == 0 else 2


if __name__ == "__main__":
    raise SystemExit(main())
