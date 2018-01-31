import time
import requests
import MySQLdb
import MySQLdb.cursors
from datetime import datetime
from datetime import date
import sys
import os

def defineMYSQL():
	db = MySQLdb.connect(host="localhost", user="root", passwd="secret", db="cash", cursorclass=MySQLdb.cursors.DictCursor)       
	return db

def getCashValue(bypass=False):
	db = defineMYSQL()
	cur = db.cursor()
	cur.execute("SELECT * FROM cash_calls ORDER BY time_created DESC LIMIT 1")
	data = cur.fetchone()
	if data:		
		t1 = datetime.now()  #.strftime("%Y-%m-%d %H:%M:%S")
		t2 = datetime.strptime(str(data['time_created']), "%Y-%m-%d %H:%M:%S")
		difference = t1 - t2
		
		if difference.seconds >= 60 or bypass == True:
			try:
				response = requests.get('http://www.floatrates.com/daily/usd.json','GET')
				data = response.json()
				KRW = str(data['krw']['rate'])
				EUR = str(data['eur']['rate'])
				CNY = str(data['cny']['rate'])
				cur.execute("INSERT INTO pairs (symbol,price) VALUES (%s,%s)",['EUR',EUR])
				cur.execute("INSERT INTO pairs (symbol,price) VALUES (%s,%s)",['CNY',CNY])
				cur.execute("INSERT INTO pairs (symbol,price) VALUES (%s,%s)",['KRW',KRW])
				cur.execute("INSERT INTO cash_calls (empty_entry) VALUES (%s)",[0])
				db.commit()
				cur.close()
				print('Data Inserted!')
				return True
			except Exception, e:
				print("Error getting EUR price from currencylayer: "+str(e))
				return False
			
		else:
			cur.close()
	    	return False
	else:
		try:
			response = requests.get('http://apilayer.net/api/live?access_key=d34c5b9323018f97953060ab60a07f4b&currencies=EUR,CNY','GET')
			data = response.json()
			response2 = requests.get('http://www.floatrates.com/daily/krw.json','GET')
			data2 = response2.json()
			KRW = str(data2['usd']['rate'])
			EUR=str(data['quotes']['USDEUR'])
			CNY=str(data['quotes']['USDCNY'])
			cur.execute("INSERT INTO pairs (symbol,price) VALUES (%s,%s)",['EUR',EUR])
			cur.execute("INSERT INTO pairs (symbol,price) VALUES (%s,%s)",['CNY',CNY])
			cur.execute("INSERT INTO pairs (symbol,price) VALUES (%s,%s)",['KRW',KRW])
			cur.execute("INSERT INTO cash_calls (empty_entry) VALUES (%s)",[0])
			db.commit()
			cur.close()
			print('Data Inserted!')
			return True
		except Exception, e:
			print("Error getting EUR price from currencylayer: "+str(e))
			return False

def createTables():
	print("Creating tables...")
	db = defineMYSQL()
	cur = db.cursor()
	cur.execute("CREATE TABLE cash_calls (id INT(255) AUTO_INCREMENT PRIMARY KEY, empty_entry TINYINT(1), time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)")
	cur.execute("CREATE TABLE pairs (id INT(255) AUTO_INCREMENT PRIMARY KEY,symbol VARCHAR(150), price VARCHAR (255), time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)")
	db.commit()
	cur.close()
	print("Done!")

#main process:


location = os.path.realpath("")
if len(sys.argv)>1:
	arg=sys.argv[1]
	if (arg == "-create"):
		createTables()
	if (arg == "-bypass"):
		getCashValue(True)
while True:
	print("Executing loop")
	getCashValue()
	print("Sleeping")
	time.sleep(60) # seconds delay