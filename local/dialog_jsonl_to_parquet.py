#!/usr/bin/env python3

import json
import sys

import pyarrow as pa
import pyarrow.parquet as pq


SCHEMA = pa.schema([
    pa.field("doc_id", pa.string(), nullable=True),
    pa.field("dialog_id", pa.string(), nullable=False),
    pa.field("subtitle", pa.string(), nullable=True),
    pa.field("participants", pa.list_(pa.field("item", pa.string(), nullable=False)), nullable=False),
    pa.field("metadata_json", pa.string(), nullable=True),
    pa.field("metadata_types_json", pa.string(), nullable=True),
    pa.field("turns", pa.list_(pa.field("item", pa.struct([
        pa.field("turn_id", pa.string(), nullable=False),
        pa.field("sequence_no", pa.uint32(), nullable=False),
        pa.field("speaker", pa.string(), nullable=True),
        pa.field("raw_author", pa.string(), nullable=True),
        pa.field("text", pa.string(), nullable=False),
        pa.field("kind", pa.string(), nullable=False),
        pa.field("author_compact_start", pa.uint32(), nullable=True),
        pa.field("author_compact_stop", pa.uint32(), nullable=True),
        pa.field("content_compact_start", pa.uint32(), nullable=True),
        pa.field("content_compact_stop", pa.uint32(), nullable=True),
    ]), nullable=False)), nullable=False),
    pa.field("tokens", pa.list_(pa.field("item", pa.struct([
        pa.field("token_id", pa.uint64(), nullable=False),
        pa.field("turn_id", pa.string(), nullable=True),
        pa.field("segment_type", pa.string(), nullable=False),
        pa.field("sequence_no", pa.uint32(), nullable=False),
        pa.field("orth", pa.string(), nullable=False),
        pa.field("lemma", pa.string(), nullable=True),
        pa.field("pos", pa.string(), nullable=True),
        pa.field("compact_start", pa.uint32(), nullable=False),
        pa.field("compact_stop", pa.uint32(), nullable=False),
        pa.field("local_start", pa.uint32(), nullable=False),
        pa.field("local_stop", pa.uint32(), nullable=False),
        pa.field("eos", pa.bool_(), nullable=False),
    ]), nullable=False)), nullable=False),
    pa.field("annotations", pa.list_(pa.field("item", pa.struct([
        pa.field("obj_id", pa.uint64(), nullable=False),
        pa.field("turn_id", pa.string(), nullable=True),
        pa.field("segment_type", pa.string(), nullable=False),
        pa.field("compact_start", pa.uint32(), nullable=False),
        pa.field("compact_stop", pa.uint32(), nullable=False),
        pa.field("local_start", pa.uint32(), nullable=False),
        pa.field("local_stop", pa.uint32(), nullable=False),
        pa.field("text", pa.string(), nullable=False),
        pa.field("annotation_set", pa.string(), nullable=True),
        pa.field("annotation_type", pa.string(), nullable=False),
        pa.field("lemma", pa.string(), nullable=True),
        pa.field("stage", pa.string(), nullable=True),
        pa.field("source", pa.string(), nullable=True),
        pa.field("attributes_json", pa.string(), nullable=True),
    ]), nullable=False)), nullable=False),
    pa.field("relations", pa.list_(pa.field("item", pa.struct([
        pa.field("obj_id", pa.uint64(), nullable=False),
        pa.field("relation_set", pa.string(), nullable=True),
        pa.field("relation_type", pa.string(), nullable=False),
        pa.field("source_obj_id", pa.uint64(), nullable=False),
        pa.field("target_obj_id", pa.uint64(), nullable=False),
        pa.field("source_turn_id", pa.string(), nullable=True),
        pa.field("target_turn_id", pa.string(), nullable=True),
        pa.field("source_segment_type", pa.string(), nullable=True),
        pa.field("target_segment_type", pa.string(), nullable=True),
    ]), nullable=False)), nullable=False),
])


def normalize_record(record):
    record.setdefault("doc_id", None)
    record.setdefault("dialog_id", "")
    record.setdefault("subtitle", None)
    record.setdefault("participants", [])
    record.setdefault("metadata_json", None)
    record.setdefault("metadata_types_json", None)
    record.setdefault("turns", [])
    record.setdefault("tokens", [])
    record.setdefault("annotations", [])
    record.setdefault("relations", [])

    if not record["dialog_id"]:
        raise ValueError("dialog_id is required")

    record["participants"] = [str(item) for item in record["participants"]]

    for item in record["turns"]:
        item["turn_id"] = str(item["turn_id"])
        item["sequence_no"] = int(item["sequence_no"])
        if item.get("speaker") is not None:
            item["speaker"] = str(item["speaker"])
        if item.get("raw_author") is not None:
            item["raw_author"] = str(item["raw_author"])
        item["text"] = str(item["text"])
        item["kind"] = str(item["kind"])
        for field in ("author_compact_start", "author_compact_stop", "content_compact_start", "content_compact_stop"):
            if item.get(field) is not None:
                item[field] = int(item[field])

    for item in record["tokens"]:
        item["token_id"] = int(item["token_id"])
        if item.get("turn_id") is not None:
            item["turn_id"] = str(item["turn_id"])
        item["segment_type"] = str(item["segment_type"])
        item["sequence_no"] = int(item["sequence_no"])
        item["orth"] = str(item["orth"])
        if item.get("lemma") is not None:
            item["lemma"] = str(item["lemma"])
        if item.get("pos") is not None:
            item["pos"] = str(item["pos"])
        item["compact_start"] = int(item["compact_start"])
        item["compact_stop"] = int(item["compact_stop"])
        item["local_start"] = int(item["local_start"])
        item["local_stop"] = int(item["local_stop"])
        item["eos"] = bool(item["eos"])

    for item in record["annotations"]:
        item["obj_id"] = int(item["obj_id"])
        if item.get("turn_id") is not None:
            item["turn_id"] = str(item["turn_id"])
        item["segment_type"] = str(item["segment_type"])
        item["compact_start"] = int(item["compact_start"])
        item["compact_stop"] = int(item["compact_stop"])
        item["local_start"] = int(item["local_start"])
        item["local_stop"] = int(item["local_stop"])
        item["text"] = str(item["text"])
        item["annotation_type"] = str(item["annotation_type"])

    for item in record["relations"]:
        item["obj_id"] = int(item["obj_id"])
        item["relation_type"] = str(item["relation_type"])
        item["source_obj_id"] = int(item["source_obj_id"])
        item["target_obj_id"] = int(item["target_obj_id"])
        for field in ("source_turn_id", "target_turn_id", "source_segment_type", "target_segment_type"):
            if item.get(field) is not None:
                item[field] = str(item[field])

    return record


def main():
    if len(sys.argv) != 3:
        print("Usage: dialog_jsonl_to_parquet.py <input.jsonl> <output.parquet>", file=sys.stderr)
        return 1

    input_path, output_path = sys.argv[1], sys.argv[2]
    records = []
    with open(input_path, "r", encoding="utf-8") as handle:
        for line in handle:
            line = line.strip()
            if not line:
                continue
            records.append(normalize_record(json.loads(line)))

    table = pa.Table.from_pylist(records, schema=SCHEMA)
    pq.write_table(table, output_path, compression="NONE")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
