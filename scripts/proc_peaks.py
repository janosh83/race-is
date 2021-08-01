import json
import re
from bs4 import BeautifulSoup
""" 
creates json file which is then imported into aplication from kml map data with peaks
""" 

js = []
#extractor = URLExtract()

link_regex = re.compile('((https?):((//)|(\\\\))+([\w\d:#@%/;$()~_?\+-=\\\.&](#!)?)*)', re.DOTALL)

inputfile = "streka_vrcholy.kml"
with open(inputfile, 'r', encoding="utf8") as f:
  soup = BeautifulSoup(f, "xml")

  i = 1
  for node in soup.find_all('Placemark'):
       
       name = node.contents[1].string
       name = name.replace("\n","")
       coords = node.Point.coordinates.string
       desc = node.contents[3].string

       #urls = extractor.find_urls(desc)
       urls = re.findall(link_regex, desc)
       print(urls)

       for url in urls:
         atag = "<a href=\"{}\" target=\"_blank\">{}</a>".format(url[0],url[0])
         print(atag)
         desc = desc.replace(url[0],atag)

       #print("desc:",desc)

       clist = coords.split(",")
       lat = clist[1].strip()
       lon = clist[0].strip()

       peak = {}
       #peak["short_id"] = "jizda_{}".format(i)
       peak["short_id"] = ""
       peak["title"] = name
       peak["description"] = desc
       peak["latitude"] = float(lat)
       peak["longitude"] = float(lon)
       peak["points"] = 1

       js.append(peak)

       #if i == 20:
       #  break
       i = i + 1

with open("streka_vrcholy.json","w", encoding="utf-8") as f:
  json.dump(js, f, ensure_ascii=False)