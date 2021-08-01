from bs4 import BeautifulSoup


""" 
process kml files for import into maps.m
"""
inputfile = "streka_vrcholy.kml"
with open(inputfile, 'r', encoding="utf8") as f:
    soup = BeautifulSoup(f, "xml")
    i = 1

    for node in soup.find_all('Placemark'):
       
        #name = node.contents[1].string
        #coords = node.Point.coordinates.string
        #desc = node.contents[3].string
        #node.contents[1].string = "(jizda_{}) ".format(i) + node.contents[1].string
        node.contents[3].string = ""

        i = i + 1

    with open("streka_vrcholy.xml","w", encoding="utf-8") as f:
        f.write(str(soup))