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
	print '\nimport...\n from %(filename)s\n to %(hostname)s:%(database)s' % (db_dict)

	cmd = ["mysql -P %(port)s -h %(hostname)s -u %(username)s -p%(password)s %(database)s < %(filename)s" % (db_dict)]
	proc_export = subprocess.Popen(cmd, shell = True)
	proc_export.wait()



def export_db_to_file(db_dict, filename):
	u"""
	Export database to file
	"""
	db_dict['filename'] = filename
	print '\nexport...\n from %(hostname)s:%(database)s\n to %(filename)s' % (db_dict)

	cmd = ["mysqldump -P %(port)s -h %(hostname)s -u %(username)s -p%(password)s %(database)s > %(filename)s" % (db_dict)]
			  
	proc_export = subprocess.Popen(cmd, shell = True)
	proc_export.wait()


def db_execute(db_dict, sql, select = False):
	u"""
	Ustanawianie połączenia z bazą danych
	"""
	print '\n', sql, '\nSTATUS - ',
	out = tuple()
	try:
		connection = MySQLdb.connect(user=db_dict['username'], passwd=db_dict['password'], host=db_dict['hostname'], db=db_dict['database'], charset = "utf8", use_unicode = True)
		with closing( connection.cursor() ) as cursor:
			cursor.execute(sql)
			if select:
				out = cursor.fetchall()
			connection.commit()
		connection.close()
		print 'OK\n'		
	except MySQLdb.Warning as myw:
		print 'MySQLdb.Warning\n', myw.__class__.__name__, myw
	except Exception as ex:
		print 'Exception\n', ex.__class__.__name__, ex
	return out


def clear_database(db_dict, corpus):
	sql_corpus_owner = "INSERT INTO users (`user_id` ,`login` ,`screename` ,`password`) VALUES (NULL , 'corpus', 'corpus owner', MD5('corpus'))"
	db_execute(db_dict, sql_corpus_owner)
	sql_corpus_owner = "SELECT user_id FROM users WHERE login = 'corpus'"
	user_id = db_execute(db_dict, sql_corpus_owner, True)[0]

	sql = []
	sql.append("DELETE FROM reports_limited_access ")
	sql.append("DELETE FROM corpus_perspective_roles ")
	sql.append("DELETE FROM users_corpus_roles ")
	sql.append("DELETE FROM users_roles ")
	sql.append("DELETE FROM user_activities ")

	sql.append("DELETE FROM corpora " + add_where(corpus, 'id'))

	sql.append("UPDATE corpora SET user_id = %s " % (user_id) + add_where(corpus, 'id', "=") )
	sql.append("UPDATE reports SET user_id = %s " % (user_id) + add_where(corpus, 'corpora', "=") )
	sql.append("UPDATE relations SET user_id = %s " % (user_id) )
	sql.append("UPDATE reports_annotations_optimized SET user_id = %s " % (user_id) )
	sql.append("UPDATE reports_annotations_attributes SET user_id = %s " % (user_id) )
	sql.append("UPDATE reports_diffs SET user_id = %s " % (user_id) )

	sql.append("DELETE FROM users WHERE user_id != '%s'" % (user_id))
	sql.append("DELETE FROM reports_types WHERE id != '1'")

	sql.append("INSERT INTO users (`user_id` ,`login` ,`screename` ,`password`) VALUES (NULL , 'admin', 'Inforex Admin', MD5('admin'))")	

	for delete in sql:
		db_execute(db_dict, delete)

	sql_select = "SELECT ext FROM corpora GROUP BY ext"
	sql_drop = ["tags", "pcsn_age_ranges"]
	for ext in db_execute(db_dict, sql_select, True):
		if len("%s" % (ext)):
			sql_drop.append(ext)
	
	for table in sql_drop:
		db_execute(db_dict, "DROP TABLE IF EXISTS %s " % (table))

	for table in db_execute(db_dict, "SHOW TABLES", True):
		db_execute(db_dict, "ALTER TABLE %s AUTO_INCREMENT = 1" % (table))
		
	sql_corpus_owner = "SELECT user_id FROM users WHERE login = 'admin'"
	user_id = db_execute(db_dict, sql_corpus_owner, True)[0]
	db_execute(db_dict, "INSERT INTO users_roles(user_id, role) VALUES(%d, 'admin')" % (user_id))


def add_where(elements, name, operator = "!="):
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
		clear_database(db_dict, config.get('save', 'corpus').split())

	if options.export_db:
		export_db_to_file(db_dict, config.get('file', 'name_out'))

if __name__=="__main__":
	main()
