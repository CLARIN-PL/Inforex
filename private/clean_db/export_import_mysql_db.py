#!/usr/bin/env python
# -*- coding: utf-8 -*-

import subprocess, sys, ConfigParser
from optparse import OptionParser
from contextlib import closing
import MySQLdb, warnings

script_usage = "python {0} -c config_file".format(sys.argv[0])
warnings.filterwarnings('error', category=MySQLdb.Warning)


def import_db_from_file(db_dict, filename):
    u"""
    Import database from file
    """
    db_dict['filename'] = filename
    print('\nimport...\n from %(filename)s\n to %(hostname)s:%(database)s' % (db_dict))

    cmd = ["mysql -P %(port)s -h %(hostname)s -u %(username)s -p%(password)s %(database)s < %(filename)s" % (db_dict)]
    proc_export = subprocess.Popen(cmd, shell = True)
    proc_export.wait()


def export_db_to_file(db_dict, filename):
    u"""
    Export database to file
    """
    db_dict['filename'] = filename
    print('\nexport...\n from %(hostname)s:%(database)s\n to %(filename)s' % (db_dict))
    cmd = ["mysqldump -P %(port)s -h %(hostname)s -u %(username)s -p%(password)s %(database)s > %(filename)s" % (db_dict)]
    proc_export = subprocess.Popen(cmd, shell=True)
    proc_export.wait()


def db_execute(db_dict, sql, select = False):
    u"""
    Ustanawianie połączenia z bazą danych
    """
    print(sql)
    out = tuple()
    try:
        connection = MySQLdb.connect(user=db_dict['username'], passwd=db_dict['password'], host=db_dict['hostname'], db=db_dict['database'], charset = "utf8", use_unicode = True)
        with closing( connection.cursor() ) as cursor:
            cursor.execute(sql)
            if select:
                out = cursor.fetchall()
            connection.commit()
        connection.close()
        print('OK')
    except MySQLdb.Warning as myw:
        print('MySQLdb.Warning\n', myw.__class__.__name__, myw)
    except Exception as ex:
        print('Exception\n', ex.__class__.__name__, ex)
    return out


def clear_database(db_dict):
    sql = []
    sql.append("DELETE FROM tokens_tags_optimized ")
    sql.append("DELETE FROM tokens_tags_ctags ")
    sql.append("DELETE FROM tokens_backup ")
    sql.append("DELETE FROM tokens ")
    sql.append("DELETE FROM bases ")

    sql.append("DELETE FROM annotation_sets_corpora ")

    sql.append("DELETE FROM reports_and_images ")
    sql.append("DELETE FROM images ")



    sql.append("DELETE FROM reports_limited_access ")
    sql.append("DELETE FROM corpus_perspective_roles ")
    sql.append("DELETE FROM users_corpus_roles ")
    sql.append("DELETE FROM users_roles ")
    sql.append("DELETE FROM user_activities ")
    sql.append("DELETE FROM activities ")
    sql.append("DELETE FROM activity_types ")
    sql.append("DELETE FROM flag_status_history; ")

    sql.append("DELETE FROM annotation_types_attributes_enum ")
    sql.append("DELETE FROM ips ")
    sql.append("DELETE FROM ccl_viewer ")
    sql.append("DELETE FROM reports_users_selection ")
    sql.append("DELETE FROM annotation_types_shortlist")

    sql.append("DELETE FROM shared_attributes_enum ")
    sql.append("DELETE FROM shared_attributes ")
    sql.append("DELETE FROM annotation_types_attributes_enum ")
    sql.append("DELETE FROM reports_annotations_shared_attributes ")
    sql.append("DELETE FROM reports_annotations_optimized")
    sql.append("DELETE FROM reports_annotations_attributes")

    sql.append("DELETE FROM relations_groups ")
    sql.append("DELETE FROM relation_sets")
    sql.append("DELETE FROM relation_types ")
    sql.append("DELETE FROM relations ")

    sql.append("DELETE FROM tasks_reports ")
    sql.append("DELETE FROM tasks ")

    sql.append("DELETE FROM reports_events_slots")
    sql.append("DELETE FROM reports_events")
    sql.append("DELETE FROM event_groups")
    sql.append("DELETE FROM event_type_slots")
    sql.append("DELETE FROM event_types")

    sql.append("DELETE FROM reports_flags ")
    sql.append("DELETE FROM reports_diffs ")
    sql.append("UPDATE reports SET parent_report_id = NULL ")
    sql.append("DELETE FROM reports ")

    sql.append("DELETE FROM corpus_roles")
    sql.append("DELETE FROM corpus_subcorpora")

    sql.append("DELETE FROM export_errors")
    sql.append("DELETE FROM exports")
    sql.append("DELETE FROM corpora_flags ")
    sql.append("DELETE FROM corpora_relations")
    sql.append("DELETE FROM corpus_and_report_perspectives")
    sql.append("DELETE FROM corpora")

    sql.append("DELETE FROM users_roles")
    sql.append("DELETE FROM users")

    sql.append("DELETE FROM annotation_sets WHERE annotation_set_id <> 1")

    for s in sql:
        db_execute(db_dict, s)

    # Usunięcie niepotrzebnych tabel
    sql_drop = ["tags", "pcsn_age_ranges", "users_checkboxes", "annotation_types_common"]
    for table in db_execute(db_dict, "SHOW TABLES", True):
        if ("%s" % table).startswith("reports_ext_"):
            sql_drop.append(table)

    for table in sql_drop:
        db_execute(db_dict, "DROP TABLE IF EXISTS %s " % (table))

    # Reset kluczy
    for table in db_execute(db_dict, "SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'", True):
        db_execute(db_dict, "ALTER TABLE %s AUTO_INCREMENT = 1" % (table[0]))

    db_execute(db_dict, "INSERT INTO users (`user_id` ,`login` ,`screename` ,`password`) VALUES (NULL , 'admin', 'Inforex Admin', MD5('admin'))")

    sql_corpus_owner = "SELECT user_id FROM users WHERE login = 'admin'"
    user_id = db_execute(db_dict, sql_corpus_owner, True)[0]
    db_execute(db_dict, "INSERT INTO users_roles(user_id, role) VALUES(%d, 'admin')" % (user_id))

    # Ustawienie collation
    collation = "utf8mb4_unicode_ci"
    for table in db_execute(db_dict, "SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'", True):
        db_execute(db_dict, "ALTER TABLE %s COLLATE %s" % (table[0], collation))
    #    for column in db_execute(db_dict, "SHOW COLUMNS FROM %s WHERE Type LIKE '%char%' or Type LIKE '%text%'" % table[0]):
    #        sql =
    #        db_execute(db_dict, "ALTER TABLE %s MODIFY %s %s COLLATE %s" % (table[0], collation))



def add_where(elements, name, operator="!="):
    join_text = "' AND " + name + " " + operator + " '"
    return "" if not len(elements) else (" WHERE " + name + " " + operator + " '" + join_text.join(elements) + "'" )


def main():
    parser = OptionParser(usage = script_usage, version = "%prog 0.1")
    parser.add_option('-c', '--config', action = "store", type = "string", dest = "config", default = './config_file.ini',\
        help = "config file")
    parser.add_option("-i", "--import", action = "store_true", dest = "import_db", default = False, help = "import from file process (false)")
    parser.add_option("-e", "--export", action = "store_true", dest = "export_db", default = False, help = "export to file process (false)")
    parser.add_option("-d", "--db_clear", action = "store_true", dest = "clear_db", default = False, help = "clear database process (false)")

    (options, argv) = parser.parse_args()
    config = ConfigParser.ConfigParser()
    config.read(options.config)

    db_dict = {'username': config.get('db', 'username'),
        'password': config.get('db', 'password'),
        'hostname': config.get('db', 'hostname'),
        'port': config.get('db', 'port'),
        'database': config.get('db', 'database')}

    if options.import_db:
        import_db_from_file(db_dict, config.get('file', 'name_in'))

    if options.clear_db:
        clear_database(db_dict)

    if options.export_db:
        export_db_to_file(db_dict, config.get('file', 'name_out'))


if __name__=="__main__":
    main()
