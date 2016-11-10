
import feedparser
import MySQLdb
import time
import smtplib
from email.mime.text import MIMEText

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

#Load all user data as tuples in a tuple.
query="SELECT * FROM users WHERE 1";
cursor.execute(query)
userlist=cursor.fetchall()

#Loop through all users and build a SQL query from each user's wishlist (user[2]). 
for user in userlist:
    query="SELECT * FROM reasent WHERE ( "
    #Split the wishlist string (user[2]) into a list where each line from the wishlist is an element.
    wishlist=user[2].splitlines()
    for x in range(len(wishlist)):
        #split each line in the wishlist into a list where each word from the line is an element	
        words=wishlist[x].split()
        if len(words)==1:
            query+="title LIKE '%"+words[0]+"%'"
        else:
            query+="( "
            for y in range(len(words)):
                query+="title LIKE '%"+words[y]+"%'"
                if y!=len(words)-1:
                    query+=" AND "
            query+=" )"
        if x!=len(wishlist)-1:
            query+=" OR "
    query+=" ) AND new=true ORDER BY time"

    #Execute SQL query and save a list of movies to be emailed in variable hitlist
    cursor.execute(query)
    hitlist=cursor.fetchall()
    if len(hitlist)!=0:
        msgbody=""
        for hit in hitlist:
            msgbody+=hit[2]+" : <a href='"+hit[4]+"'>"+hit[3].replace("'", r"\'")+"</a><br>"
        msg = MIMEText(msgbody.encode('utf-8'),'html', _charset='utf-8')
        msg['From'] = user[0]
        msg['To'] = user[0]
        msg['MIME-Version'] = '1.0'
        msg['Content-type'] = 'text/html'
        msg['Subject'] = 'Your Movies!'
        try:
            server_ssl = smtplib.SMTP_SSL("smtp.gmail.com", 465)
            server_ssl.ehlo() 
            server_ssl.login('nils.leandersson@gmail.com','rocket289')  
            server_ssl.sendmail(sender, [to], msg.as_string())
            server_ssl.close()
            print ('mail sent successfully to '+user[0])
        except:
            print ('sending mail failed to '+user[0])
    else:
        print ('Nothing new to send to '+user[0])

#Delete all movie items older than one week from reasent table 
cursor.execute("DELETE FROM reasent WHERE time<"+str(time.time()-604800))

#Mark all movie items as sent in reasent table
cursor.execute("UPDATE reasent SET new=false WHERE 1")
db.commit()

db.close()


