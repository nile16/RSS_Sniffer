import smtplib
from email.mime.text import MIMEText

def sanitize_wishlist(list):
    #Split the wishlist string (user[2]) submitted by the user into a wishlist where each line is an element.
    list=list.splitlines()
    #Strip strings from leading and trailing whitespace.
    list=map(unicode.strip,list)
    #Filter wishlist from empty strings.
    list=filter(None,list)
    return (list)    

def build_sql_query(wishlist):
    query="SELECT * FROM reasent WHERE ( "
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
    return (query)

def email_list(addr,hitlist):
    msgbody=""
    for hit in hitlist:
        msgbody+=hit[2]+" : <a href='"+hit[4]+"'>"+hit[3].replace("'", r"\'")+"</a><br>"
    msg = MIMEText(msgbody.encode('utf-8'),'html', _charset='utf-8')
    msg['From'] = 'Movie RSS Sniffer'
    msg['To']   = addr
    msg['MIME-Version'] = '1.0'
    msg['Content-type'] = 'text/html'
    msg['Subject'] = 'Your Movies!'
    server_ssl = smtplib.SMTP_SSL("smtp.gmail.com", 465)
    server_ssl.ehlo() 
    server_ssl.login('nils.leandersson@gmail.com','xxxxx')  
    server_ssl.sendmail('Movie RSS Sniffer', addr, msg.as_string())
    server_ssl.close()
