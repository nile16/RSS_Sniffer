
import feedparser
import MySQLdb
import time
import sabuem

#Two tables, 'reasent' and 'users' must be defined before running this script. 
#    CREATE TABLE `reasent` ( `new` boolean, `time` int(11), `source` varchar(16), `title` varchar(1024), `link` varchar(256))
#        ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
#    CREATE TABLE `users` ( `email` varchar(64), `pswd` varchar(64), `list` text) ENGINE=InnoDB DEFAULT CHARSET=utf8
db = MySQLdb.connect(host="localhost", user='root', passwd='apa', db='shows', use_unicode=True, charset="utf8")
cursor = db.cursor()

#Parse RSS feeds into variables in the form of dicts. Every dict has a key 'entries' which value is a list. 
#The list items represent movies in the form of dicts. These movie dicts have keys like 'title' and 'links' 
yifi    = feedparser.parse('https://yts.ag/rss')
extra1  = feedparser.parse('http://extratorrent.cc/rss.xml?type=today&cid=4')
extra2  = feedparser.parse('http://extratorrent.cc/rss.xml?type=today&cid=8')
eztv    = feedparser.parse('https://eztv.ag/ezrss.xml')
svtplay = feedparser.parse('http://www.svtplay.se/rss.xml')
netflix = feedparser.parse('http://www.netflixnewreleases.net/feed/')

#Loop through the lists of movies and add the titles not already in the database. The value 'true' for 'new' indicates the title has not yet been matched against the user's wishlists
#Escape character ' in the titles to prevent the SQL commands from breaking.
for item in yifi['entries']:
    cursor.execute("SELECT * FROM reasent WHERE source='YIFI' AND title='"+item['title'].replace("'", r"\'")+"';")
    if (len(cursor.fetchall())==0):
        cursor.execute("INSERT INTO reasent (new,time,source,title,link)  VALUES (true,'"+str(int(time.mktime(item['published_parsed'])))+"','YIFI','"+item['title'].replace("'", r"\'")+"','"+item['links'][1]['href']+"')")

for item in extra1['entries']:
    cursor.execute("SELECT * FROM reasent WHERE source='ExtraT' AND title='"+item['title'].replace("'", r"\'")+"';")
    if (len(cursor.fetchall())==0):
        cursor.execute("INSERT INTO reasent (new,time,source,title,link)  VALUES (true,'"+str(int(time.mktime(item['published_parsed'])))+"','ExtraT','"+item['title'].replace("'", r"\'")+"','"+item['links'][1]['href']+"')")

for item in extra2['entries']:
    cursor.execute("SELECT * FROM reasent WHERE source='ExtraT' AND title='"+item['title'].replace("'", r"\'")+"';")
    if (len(cursor.fetchall())==0):
        cursor.execute("INSERT INTO reasent (new,time,source,title,link)  VALUES (true,'"+str(int(time.mktime(item['published_parsed'])))+"','ExtraT','"+item['title'].replace("'", r"\'")+"','"+item['links'][1]['href']+"')")

for item in eztv['entries']:
    cursor.execute("SELECT * FROM reasent WHERE source='EZ TV' AND title='"+item['title'].replace("'", r"\'")+"';")
    if (len(cursor.fetchall())==0):
        cursor.execute("INSERT INTO reasent (new,time,source,title,link)  VALUES (true,'"+str(int(time.mktime(item['published_parsed'])))+"','EZ TV','"+item['title'].replace("'", r"\'")+"','"+item['links'][1]['href']+"')")

for item in svtplay['entries']:
    cursor.execute("SELECT * FROM reasent WHERE source='SVT Play' AND title='"+item['title'].replace("'", r"\'")+"';")
    if (len(cursor.fetchall())==0):
        cursor.execute("INSERT INTO reasent (new,time,source,title,link)  VALUES (true,'"+str(int(time.mktime(item['published_parsed'])))+"','SVT Play','"+item['title'].replace("'", r"\'")+"','"+item['link']+"')")

for item in netflix['entries']:
    cursor.execute("SELECT * FROM reasent WHERE source='NetFlix' AND title='"+item['title'].replace("'", r"\'")+"'")
    if (len(cursor.fetchall())==0):
        cursor.execute("INSERT INTO reasent (new,time,source,title,link)  VALUES (true,'"+str(int(time.mktime(item['published_parsed'])))+"','NetFlix','"+item['title'].replace("'", r"\'")+"','"+item['link']+"')")

db.commit()

#Load data for all users as tuples in a tuple.
cursor.execute("SELECT * FROM users WHERE confirm='0'")
userdatalist=cursor.fetchall()

#Loop through all users and match each user's wishlist against new features in database. 
for userdata in userdatalist:
    wishlist=sabuem.sanitize_wishlist(userdata[2])
    if len(wishlist)!=0:
        #Build a SQL query based on each user's wishlist and execute it.
        cursor.execute(sabuem.build_sql_query(wishlist))
        hitlist=cursor.fetchall()
        if len(hitlist)!=0:
            try:
                sabuem.email_list(userdata[0],hitlist)
                print ('Mail sent successfully to '+userdata[0])
            except:
                print ('Sending mail to '+userdata[0]+' failed')
        else:
            print ('Nothing new to send to '+userdata[0])
    else:
        print ('Nothing defined in '+userdata[0]+' wishlist')

#Delete all items older than one week from reasent table 
cursor.execute("DELETE FROM reasent WHERE time<"+str(time.time()-604800))

#Delete unconfirmed accounts older than one hour 
cursor.execute("DELETE FROM users WHERE confirm!='0' AND regtime<"+str(time.time()-3600))

#Mark all items as sent in reasent table
cursor.execute("UPDATE reasent SET new=false WHERE 1")
db.commit()

db.close()


