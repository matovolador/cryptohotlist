import time
import requests
import MySQLdb
import MySQLdb.cursors
from datetime import datetime
from datetime import date
import sys

import logging

def DefineMYSQL():
	db = MySQLdb.connect(host="localhost", user="root", passwd="secret", db="crypto_hotlist", cursorclass=MySQLdb.cursors.DictCursor)       
	return db

def CanRunAPI():
	db = DefineMYSQL()
	cur = db.cursor()
	cur.execute("SELECT * FROM api_calls ORDER BY time_created DESC LIMIT 1")
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

def GetDataFromAPI():
	print("Getting Data from API...")
	# get data from API
	try:
		response = requests.get('https://api.coinmarketcap.com/v1/ticker/','GET')
		data = response.json()
		return data
	except Exception, e:
		print("Error getting data from API: "+str(e))
		return False

def InsertData(data):
	print("Inserting data..."+str(datetime.now()))
	db = DefineMYSQL()
	cur = db.cursor()
	today = datetime.today().date()
	today = today.strftime("%Y-%m-%d")

	for i in data:
		# check if symbol exists. if not, create entry:
		cur.execute("SELECT * FROM symbols WHERE name = %s LIMIT 1",[i['name']])
		symbol=cur.fetchone()
		if (symbol == False):
			cur.execute("INSERT INTO symbols (symbol,name) VALUES(%s,%s)",[i['symbol'],i['name']])
		

		five = 0
		# calculate price_percent increment since last price_usd:
		cur.execute("SELECT * FROM prices WHERE name = %s ORDER BY id DESC LIMIT 1",[i['name']])
		last_price = cur.fetchone()
		if (last_price!= None and last_price['price_usd'] != None and i['price_usd'] != None):
			increment = float (i['price_usd']) * 100 / float(last_price['price_usd']) -100
			increment='{0:.2f}'.format(increment)
			increment = ""+increment
			five = increment
			cur.execute("INSERT INTO prices (symbol,name,price_usd,price_percent,date_created) VALUES(%s,%s,%s,%s,%s)",[i['symbol'],i['name'],i['price_usd'],increment,today])
		else:
			cur.execute("INSERT INTO prices (symbol,name,price_usd,price_percent,date_created) VALUES(%s,%s,%s,%s,%s)",[i['symbol'],i['name'],i['price_usd'],"0",today])
			five = "0"
		
		cur.execute("INSERT INTO ranks (symbol,name,rank,date_created) VALUES(%s,%s,%s,%s)",[i['symbol'],i['name'],i['rank'],today])
		
		# calculate market_cap_percent increment since last market_cap_usd:
		cur.execute("SELECT * FROM market_cap WHERE name = %s ORDER BY id DESC LIMIT 1",[i['name']])
		last_market = cur.fetchone()
		if (last_market!= None and last_market['market_cap_usd'] != None and i['market_cap_usd'] != None):
			increment = float (i['market_cap_usd']) * 100 / float(last_market['market_cap_usd']) -100
			increment='{0:.2f}'.format(increment)
			increment = ""+increment
			cur.execute("INSERT INTO market_cap (symbol,name,market_cap_usd, market_cap_percent,date_created) VALUES(%s,%s,%s,%s,%s)",[i['symbol'],i['name'],i['market_cap_usd'],increment,today])
		else:
			cur.execute("INSERT INTO market_cap (symbol,name,market_cap_usd, market_cap_percent,date_created) VALUES(%s,%s,%s,%s,%s)",[i['symbol'],i['name'],i['market_cap_usd'],"0",today])

		db.commit()



		#cache table
		##calculate price_five
		#five is calculated below

		##calculate price_half
		cur.execute("SELECT * FROM prices WHERE name=%s ORDER BY id DESC LIMIT 6",[i['name']])
		last_half = cur.fetchall()
		if len(last_half)==6:
			last_half = last_half[-1]
			if (last_half!= None and last_half['price_usd']!= None and i['price_usd']!=None):
				half = float (i['price_usd']) * 100 / float(last_half['price_usd']) -100
				half='{0:.2f}'.format(half)
				half = ""+half	
			else:
				half = "0"
		else:
			half = "0"

		##calculate price_hour
		cur.execute("SELECT * FROM prices WHERE name=%s ORDER BY id DESC LIMIT 12",[i['name']])
		last_hour = cur.fetchall()
		if len(last_hour)==12:
			last_hour = last_hour[-1]
			if (last_hour!= None and last_hour['price_usd']!= None and i['price_usd']!=None):
				hour = float (i['price_usd']) * 100 / float(last_hour['price_usd']) -100
				hour='{0:.2f}'.format(hour)
				hour = ""+hour	
			else:
				hour = "0"
		else:
			hour = "0"


		cur.execute("SELECT * FROM cache WHERE name=%s ORDER BY id DESC LIMIT 1",[i['name']])
		res = cur.fetchone()
		move = 0
		vol = 0
		if res is None:
			move = 0
		else:
			if res['movement'] is None:
				move = 0
			else:
				move = float(res['movement'])
		if res is None:
			vol = 0
		else:
			if res['volatility'] is None:
				vol = 0
			else:
				vol = float(res['volatility'])
		move = move + float( five )
		if float(five)<0:
			vol = vol + float(five) * (-1)
		else:
			vol = vol + float(five)
		move='{0:.2f}'.format(move)
		move = ""+move
		vol='{0:.2f}'.format(vol)
		vol = ""+vol
		cur.execute("INSERT INTO cache (symbol,name,rank,price_usd,price_five,price_half,price_hour,volatility,movement,date_created) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",[i['symbol'],i['name'],i['rank'],i['price_usd'],five,half,hour,vol,move,today])	

	cur.execute("INSERT INTO api_calls (date_created) VALUES(%s)",[today])
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
	cur.execute("CREATE TABLE api_calls (id INT(255),date_created DATE , time_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX api_time(date_created))")
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


#main process:
#log settings:
#logging.basicConfig(filename='error.log', level=logging.WARNING)
# log example: ----------
#try:
#	1/0
#except ZeroDivisionError, e:
#	logging.warning('The following error occurred, yet I shall carry on regardless: %s', e)
#--------------------------


if len(sys.argv)>1:
	arg=sys.argv[1]
	if (arg == "populate"):
		Populate()
	if (arg == "extra"):
		ExtraCalc()

while True:
	print("Executing loop")
	MainLoop()
	print("Sleeping")
	time.sleep(60 ) # seconds delay