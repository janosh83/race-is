import json
from bs4 import BeautifulSoup
from urlextract import URLExtract

js = []
extractor = URLExtract()

inputfile = "jizda_vrcholy.kml"
with open(inputfile, 'r', encoding="utf8") as f:
  soup = BeautifulSoup(f, "xml")

  i = 0
  for node in soup.find_all('Placemark'):
       
       name = node.contents[1].string
       coords = node.Point.coordinates.string
       desc = node.contents[3].string

       urls = extractor.find_urls(desc)
       print(urls)

       for url in urls:
         atag = "<a href=\"{}\" target=\"_blank\">{}</a>".format(url,url)
         print(atag)
         desc = desc.replace(url,atag)

       #print("desc:",desc)

       clist = coords.split(",")
       lat = clist[1].strip()
       lon = clist[0].strip()

       peak = {}
       peak["short_id"] = "peak_{}".format(i)
       peak["title"] = name
       peak["description"] = desc
       peak["latitude"] = float(lat)
       peak["longitude"] = float(lon)
       peak["points"] = 1

       js.append(peak)

       #if i == 20:
       #  break
       #i = i + 1

with open("jizda_vrcholy.json","w", encoding="utf-8") as f:
  json.dump(js, f, ensure_ascii=False)