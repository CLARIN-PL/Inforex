#!/usr/bin/python




import MySQLdb

db = MySQLdb.connect(host="nlp.pwr.edu.pl", 
                     user="gpw", 
                     passwd="gpw", 
                     db="gpw",
                     charset='utf8')

cur = db.cursor() 

cur.execute(
"""
SELECT rao.id, rao.report_id, rao.type_id, rao.from, rao.to, rao.text, rao.user_id, rao.creation_time, rao.stage, rao.source, ral.lemma
FROM 
    (SELECT * 
    FROM reports_annotations_optimized ra
    WHERE ra.type_id IN (282, 283, 284, 285, 305)) 
AS rao
left join reports_annotations_lemma ral 
on 
    ral.report_annotation_id = rao.id
""")

insert_string = "INSERT INTO reports_annotations_optimized (report_id, type_id, `from`, `to`, `text`, user_id, creation_time, stage, `source`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"

result = cur.fetchall()
for row in result:
    (id_, report_id, type_id, from_, to, text, user_id, creation_time, stage, source, lemma) = row
    if type_id == 283:
        type_id = 377
    elif type_id == 282:
        type_id = 378
    elif type_id == 284:
        type_id = 379
    elif type_id == 285:
        type_id = 380
    elif type_id == 305:
        type_id = 381
    
    insert_string = "INSERT INTO reports_annotations_optimized (report_id, type_id, `from`, `to`, `text`, user_id, creation_time, stage, `source`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)"
    cur.execute(insert_string, (report_id, type_id, from_, to, text, 73, creation_time, stage, source))
    if lemma:
        insert_string = "INSERT INTO reports_annotations_lemma (report_annotation_id, lemma) VALUES (%s, %s)"
        cur.execute(insert_string, (cur.lastrowid, lemma))
db.commit()
    
#PLIMEX
#annotation_subset_id = 56
#annotation_types.annotation_type_id
#plimex_date-377     | t3_date-283
#plimex_time-378     | t3_time-282
#plimex_duration-379 | t3_duration-284
#plimex_set-380      | t3_set-285
#plimex_range-381    | t3_range-305