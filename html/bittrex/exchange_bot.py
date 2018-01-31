import time
import requests
import MySQLdb
import MySQLdb.cursors
from datetime import datetime
from datetime import date
import sys
import logging
import os
from bittrex import bittrex
import csv



def DefineMYSQL():
	db = MySQLdb.connect(host="localhost", user="root", passwd="secret", db="exchange_bot", cursorclass=MySQLdb.cursors.DictCursor)       
	return db

def CanRun():
	db = DefineMYSQL()
	cur = db.cursor()
	cur.execute("SELECT * FROM bot_calls ORDER BY time_created DESC LIMIT 1")
	data = cur.fetchone()
	if data:		
		t1 = datetime.now()  #.strftime("%Y-%m-%d %H:%M:%S")
		t2 = datetime.strptime(str(data['time_created']), "%Y-%m-%d %H:%M:%S")
		difference = t1 - t2
		minutes_elapsed = difference.seconds/60
		if minutes_elapsed >= 5:
			# Call API and make DB entry:
			db.close()
			return True
		else:
			db.close()
	    	return False
	else:
		db.close()
		return True

def GetDB():
	print("Getting Data from API...")
	# get data from API
	try:
		response = requests.get('https://api.coinmarketcap.com/v1/ticker/','GET')
		data = response.json()
		return data
	except Exception, e:
		print("Error getting data from API: "+str(e))
		return False

def PerformAI():
	return


def InsertData(data):
	print("Inserting data..."+str(datetime.now()))
	db = DefineMYSQL()
	cur = db.cursor()
	today = datetime.today().date()
	today = today.strftime("%Y-%m-%d")

	
	db.commit()
	db.close()
	print("Data Inserted. Time: "+str(datetime.now()))

def MainLoop():
	if CanRunAPI():
		data = GetDataFromAPI()
		if (data):
			InsertData(data)
	return


def Populate():
	print("Populating...")
	db = DefineMYSQL()
	cur = db.cursor()
	cur.execute("CREATE TABLE symbols (id INT(150) AUTO_INCREMENT PRIMARY KEY, symbol VARCHAR(150),name VARCHAR(255), INDEX name_index(name) )")
	cur.execute("CREATE TABLE bot_calls (id INT(255),date_created DATE , time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX api_time(date_created))")
	cur.execute("CREATE TABLE ranks (id INT(255) AUTO_INCREMENT PRIMARY KEY,symbol VARCHAR(150), name VARCHAR(255),rank INT(50), date_created DATE ,time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX rank_time (date_created) )")
	cur.execute("CREATE TABLE prices (id INT(255) AUTO_INCREMENT PRIMARY KEY,symbol VARCHAR(150),name VARCHAR(255), price_usd VARCHAR(255) , price_percent VARCHAR(255), date_created DATE,time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX price_time (date_created) )" )
	cur.execute("CREATE TABLE market_cap (id INT(255) AUTO_INCREMENT PRIMARY KEY, symbol VARCHAR(150),name VARCHAR(255), market_cap_usd VARCHAR(255), market_cap_percent VARCHAR(255), date_created DATE,time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX market_time (date_created) )")

	#Cache tables:
	cur.execute("CREATE TABLE cache (id INT(255) AUTO_INCREMENT PRIMARY KEY, symbol VARCHAR(150),name VARCHAR(255), rank INT(50), price_usd VARCHAR(255), price_five VARCHAR(255), price_half VARCHAR(255), price_hour VARCHAR(255),volatility VARCHAR(255), movement VARCHAR(255),  date_created DATE,time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX cache_time (date_created) )")


	if (CanRunAPI()):
		today = datetime.today().date()
		today = today.strftime("%Y-%m-%d")
		data = GetDataFromAPI()
		for i in data:
			cur.execute("INSERT INTO symbols (symbol,name) VALUES(%s,%s)",[i['symbol'],i['name']])
			cur.execute("INSERT INTO prices (symbol,name,price_usd,price_percent,date_created) VALUES(%s,%s,%s,%s,%s)",[i['symbol'],i['name'],i['price_usd'],"0",today])
			cur.execute("INSERT INTO ranks (symbol,name,rank,date_created) VALUES(%s,%s,%s,%s)",[i['symbol'],i['name'],i['rank'],today])
			cur.execute("INSERT INTO market_cap (symbol,name,market_cap_usd, market_cap_percent,date_created) VALUES(%s,%s,%s,%s,%s)",[i['symbol'],i['name'],i['market_cap_usd'],"0",today])
			cur.execute("INSERT INTO cache (symbol,name,rank, price_usd,price_five,price_half,price_hour,date_created) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",[i['symbol'],i['name'],i['rank'],i['price_usd'],"0","0","0","0","0",today])

		cur.execute("INSERT INTO api_calls (date_created) VALUES(%s)",[today])
		db.commit()
	db.close()
	print("Database Populated")


def ExtraCalc():
	print("Making extra calculations...")
	db = DefineMYSQL()
	cur = db.cursor()
	cur.execute("SELECT * FROM symbols")
	symbols = cur.fetchall()
	for i in symbols:
		cur.execute("SELECT * FROM cache WHERE name=%s ORDER BY id DESC LIMIT 144",[i['name']])
		cache = cur.fetchall()
		move = 0
		vol = 0
		for h in reversed(cache):
			move = move + float( h['price_five'] )
			if float(h['price_five'])<0:
				vol = vol + float(h['price_five']) * (-1)
			else:
				vol = vol + float(h['price_five'] )
		move='{0:.2f}'.format(move)
		move = ""+move
		vol='{0:.2f}'.format(vol)
		vol = ""+vol
		cur.execute("SELECT * FROM cache WHERE name = %s ORDER BY id DESC LIMIT 1",[i['name']])
		res = cur.fetchone()
		id = res['id']
		cur.execute("UPDATE cache SET volatility=%s, movement=%s WHERE id=%s",[vol,move,id])

	db.commit()
	db.close()
	print("Extra calculations done")

def APITest():
	print("Fetching data...")
	res = api.getmarkets()
	ob = []
	h = 0
	for i in res:
		if (i['IsActive']):
			ob.extend([{}])
			ob[len(ob)-1]['MarketName'] = str(i['MarketName'])
			res2 = api.getmarketsummary(ob[len(ob)-1]['MarketName'].lower())
			ob[len(ob)-1]['Volume'] = str(res2[0]['Volume'])
			ob[len(ob)-1]['TimeStamp'] = str(res2[0]['TimeStamp'])
		#if i[0]['isActive'] == True:
	
	print("Done!")
	print(ob)
	WriteCSV(ob)
	


def WriteCSV(data):
	print("Writing data to CSV...")
	f = open(location+"/out.csv", "w")
	f.write("Market Name,Volume,TimeStamp")
	f.write('\n')
	for i in data:
		line = str(i['MarketName']) + ","+str(i['Volume']) + "," + str(i['TimeStamp'])
		f.write(line)
		f.write('\n')
	f.close()
	print("Done!")
	
#main process:
#log settings:
#logging.basicConfig(filename='error.log', level=logging.WARNING)
# log example: ----------
#try:
#	1/0
#except ZeroDivisionError, e:
#	logging.warning('The following error occurred, yet I shall carry on regardless: %s', e)
#--------------------------

location = os.path.realpath("")
key = "<enter your api key>"
secret = "<enter your api secret>"
api = bittrex(key, secret)

if len(sys.argv)>1:
	arg=sys.argv[1]
	if (arg == "populate"):
		Populate()
	

while True:
	APITest()
	sys.exit()
	print("Executing loop")
	MainLoop()
	print("Sleeping")
	time.sleep(60 ) # seconds delay