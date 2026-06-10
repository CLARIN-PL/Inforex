#!/usr/bin/env python3

import json
import sys

import pyarrow as pa
import pyarrow.parquet as pq


SCHEMA = pa.schema([
    pa.field("doc_id", pa.string(), nullable=True),
    pa.field("metadata_json", pa.string(), nullable=True),
    pa.field("metadata_types_json", pa.string(), nullable=True),
    pa.field("lemma_tokens", pa.list_(pa.field("item", pa.string(), nullable=False)), nullable=False),
    pa.field("surface_tokens", pa.list_(pa.field("item", pa.string(), nullable=False)), nullable=False),
    pa.field("pos_tokens", pa.list_(pa.field("item", pa.string(), nullable=False)), nullable=False),
    pa.field("sentence_token_lens", pa.list_(pa.field("item", pa.uint32(), nullable=False)), nullable=False),
    pa.field("ners", pa.list_(pa.field("item", pa.struct([
        pa.field("lexem", pa.string(), nullable=False),
        pa.field("ner_type", pa.string(), nullable=False),
    ]), nullable=False)), nullable=False),
    pa.field("sense_links", pa.list_(pa.field("item", pa.struct([
        pa.field("obj_id", pa.uint64(), nullable=False),
        pa.field("lemma", pa.string(), nullable=False),
        pa.field("concept", pa.string(), nullable=False),
        pa.field("link", pa.string(), nullable=False),
        pa.field("score", pa.float64(), nullable=False),
    ]), nullable=False)), nullable=False),
    pa.field("geolocations", pa.list_(pa.field("item", pa.struct([
        pa.field("obj_id", pa.string(), nullable=True),
        pa.field("name", pa.string(), nullable=False),
        pa.field("geo_type", pa.string(), nullable=True),
        pa.field("base", pa.string(), nullable=True),
        pa.field("lat", pa.float64(), nullable=False),
        pa.field("lon", pa.float64(), nullable=False),
        pa.field("importance", pa.float64(), nullable=False),
    ]), nullable=False)), nullable=False),
    pa.field("annotations", pa.list_(pa.field("item", pa.struct([
        pa.field("obj_id", pa.uint64(), nullable=False),
        pa.field("start", pa.uint32(), nullable=False),
        pa.field("stop", pa.uint32(), nullable=False),
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
    ]), nullable=False)), nullable=False),
])


def normalize_record(record):
    record.setdefault("doc_id", None)
    record.setdefault("metadata_json", None)
    record.setdefault("metadata_types_json", None)
    record.setdefault("lemma_tokens", [])
    record.setdefault("surface_tokens", [])
    record.setdefault("pos_tokens", [])
    record.setdefault("sentence_token_lens", [])
    record.setdefault("ners", [])
    record.setdefault("sense_links", [])
    record.setdefault("geolocations", [])
    record.setdefault("annotations", [])
    record.setdefault("relations", [])

    if not (len(record["lemma_tokens"]) == len(record["surface_tokens"]) == len(record["pos_tokens"])):
        raise ValueError("lemma_tokens, surface_tokens and pos_tokens must have the same length")

    record["sentence_token_lens"] = [int(value) for value in record["sentence_token_lens"]]

    for item in record["ners"]:
        item["lexem"] = str(item["lexem"])
        item["ner_type"] = str(item["ner_type"])

    for item in record["sense_links"]:
        item["obj_id"] = int(item["obj_id"])
        item["lemma"] = str(item["lemma"])
        item["concept"] = str(item["concept"])
        item["link"] = str(item["link"])
        item["score"] = float(item["score"])

    for item in record["geolocations"]:
        if item.get("obj_id") is not None:
            item["obj_id"] = str(item["obj_id"])
        item["name"] = str(item["name"])
        item["lat"] = float(item["lat"])
        item["lon"] = float(item["lon"])
        item["importance"] = float(item["importance"])

    for item in record["annotations"]:
        item["obj_id"] = int(item["obj_id"])
        item["start"] = int(item["start"])
        item["stop"] = int(item["stop"])
        item["text"] = str(item["text"])
        item["annotation_type"] = str(item["annotation_type"])

    for item in record["relations"]:
        item["obj_id"] = int(item["obj_id"])
        item["relation_type"] = str(item["relation_type"])
        item["source_obj_id"] = int(item["source_obj_id"])
        item["target_obj_id"] = int(item["target_obj_id"])

    return record


def main():
    if len(sys.argv) != 3:
        print("Usage: jsonl_to_parquet.py <input.jsonl> <output.parquet>", file=sys.stderr)
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
