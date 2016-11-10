# RSS Sniffer
This script reads RSS feeds containing the latest additions to movie websites. The purpose is to find new content of interest, eg the latest episode of a certain TV-serie. Users create a wishlist of keywords to search for. 

This script is intended to be run about once an hour as a cron job. When the script is run all the feature titles from the RSS feeds are checked against a local database. If a title is already in the database it is considered old content and disregarded. If the title is not in the database it is added and considered new content. When all RSS feeds are read each user's wishlist is checked against new content in the database. Links to the new content that matches the user's wishlist is sent in an email to the user. 
