import time
import requests
import MySQLdb
import MySQLdb.cursors
from datetime import datetime
from datetime import date
import sys
import os

def defineMYSQL():
	db = MySQLdb.connect(host="localhost", user="root", passwd="secret", db="viabtc_bot", cursorclass=MySQLdb.cursors.DictCursor)       
	return db
def defineMYSQLCash():
	db = MySQLdb.connect(host="localhost", user="root", passwd="secret", db="cash", cursorclass=MySQLdb.cursors.DictCursor)       
	return db

def canRun():
	db = defineMYSQL()
	cur = db.cursor()
	cur.execute("SELECT * FROM bot_calls ORDER BY time_created DESC LIMIT 1")
	data = cur.fetchone()
	if data:		
		t1 = datetime.now()  #.strftime("%Y-%m-%d %H:%M:%S")
		t2 = datetime.strptime(str(data['time_created']), "%Y-%m-%d %H:%M:%S")
		difference = t1 - t2
		minutes_elapsed = difference.seconds/60
		if difference.seconds >= 5:
			# Call API and make DB entry:
			db.close()
			return True
		else:
			db.close()
	    	return False
	else:
		db.close()
		return True

def createTables():
	print("Creating tables...")
	db = defineMYSQL()
	cur = db.cursor()
	cur.execute("CREATE TABLE symbols (id INT(255) AUTO_INCREMENT PRIMARY KEY,symbol VARCHAR(150), name VARCHAR (255))")
	cur.execute("CREATE TABLE volumes (id INT(255) AUTO_INCREMENT PRIMARY KEY,symbol VARCHAR(150), name VARCHAR (255), volume VARCHAR(255), time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)")
	cur.execute("CREATE TABLE prices (id INT(255) AUTO_INCREMENT PRIMARY KEY, symbol VARCHAR(150), name VARCHAR(255), price VARCHAR(255), time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP )")
	cur.execute("CREATE TABLE bot_calls (id INT(255) AUTO_INCREMENT PRIMARY KEY, empty_entry TINYINT(1), time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)")
	cur.execute("CREATE TABLE caches (id INT(255) AUTO_INCREMENT PRIMARY KEY, symbol VARCHAR(150), name VARCHAR(255), volume_minute VARCHAR(255), volume_average VARCHAR(255), time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP ) ")
	db.commit()
	cur.close()
	print("Done!")

def insertData():
	print("Inserting data...")
	db = defineMYSQL()
	cur = db.cursor()
	data = getData()
	if (data!=False):
		cur.execute("INSERT INTO symbols (symbol, name) VALUES (%s,%s)",[data['symbol'],data['name']])
		cur.execute("INSERT INTO volumes (symbol,name,volume) VALUES (%s,%s,%s)",[data['symbol'],data['name'],data['volume']])
		cur.execute("INSERT INTO prices (symbol,name,price) VALUES (%s,%s,%s)",[data['symbol'],data['name'],data['price']])
		#Do cache calculations:
		#cur.execute("INSERT INTO caches (symbol,name,volume) VALUES (%s,%s,%s)",[data['symbol'],data['name'],data['volume']])  TODO

		cur.execute("INSERT INTO bot_calls (empty_entry) VALUES (%s)",[0])
		db.commit()
		cur.close()
		print("Done!")
	else:
		cur.close()
		print("Could not get data from Bitfinex.")

def getData():
	print("Getting data...")
	CNY = getCNYPrice()
	if (CNY != False):
		try:
			res = requests.get('https://api.viabtc.com/v1/market/ticker?market=BCCCNY','GET')
			res = res.json()
			if (res['message']=='Ok'):
				obj = {}
				obj['name'] = 'Bitcoin Cash'
				obj['symbol'] = 'BCH'
				obj['volume'] = res['data']['ticker']['vol']
				obj['price'] = str(float(res['data']['ticker']['last'])/CNY) 
			else:
				return False
		except Exception, e:
			print("Error getting JSON from kraken: "+str(e))
			return False
		
		return obj
	else:
		return False

	
	
def getCNYPrice():
	db = defineMYSQLCash()
	cur = db.cursor()
	cur.execute("SELECT * FROM pairs WHERE symbol=%s ORDER BY id DESC LIMIT 1",['CNY'])
	CNY = cur.fetchone()
	cur.close()
	if CNY:
		return float(CNY['price'])
	else:
		print("No EUR value found. Loop skipped.")
		return False

#main process:


location = os.path.realpath("")

if len(sys.argv)>1:
	arg=sys.argv[1]
	if (arg == "-create"):
		createTables()
	if (arg == "-populate"):
		populate()
	

while True:
	print("Executing loop")
	if (canRun()):
		insertData()	
	print("Sleeping")
	time.sleep(1 ) # seconds delay



