Layercake is a project which aids in rapid web design by automating css pixel math for layout.
It uses phpquery to get pseudo-attributes, uses them to dynamically generate css, and then strips them out of the html.

It is different from every other dynamic css language in that it can size a div based on the container, whereas Sass, Less, Xcss etc. are not reading from the html and are not "aware" of the div nesting in the html file.


Installation instructions:
With a php environment running (for instance Xampp)
place layercake folder in C:\xampp\htdocs
in web browser go to http://localhost/layercake/index.php

All changes are made in C:\xampp\htdocs\layercake\input.htm
and appear at http://localhost/layercake/index.php
and http://localhost/layercake/style.css respectively

Notice that all of the "pseudo-attributes" applied in C:\xampp\htdocs\layercake\input.htm
are automatically stripped from the code in http://localhost/layercake/index.php

Things to try:
for each of the following, make the change to C:\xampp\htdocs\layercake\input.htm and refresh the browser at http://localhost/layercake/index.php
view source of index.php and style.css

change the total width of the outer most container to any number:
for example: wid="960" to wid="800"

change the "wid" fractions of the three columns (leftnav, content, sidebar) to any combination of fractions that add up to one.
for example: 1/3 1/3 1/3

change the default values for border, padding and margin to see them change for all divs:

in class="defaults" change the widths of margin: amn="5" to amn="10" 
border abd="1" to abd="3"
padding apd="10" to apd="5"

how spacing works:

for every div: width of this div's container divided by fraction = outerwidth
outerwidth-(total margin + total border + total padding) = width
every div can be a container for another div. try nesting some additional divs.

override defaults locally:

in content div add amn="0" the page defaults were overridden for this div only.
in content div add rbd="5" the right border only was overridden.

How edges work:

For a given edge value, for instance "tmn" for margin-top: 
the program first checks for a locally defined value for the div "tmn", if that is not found
it checks for a locally defined value for all of the margins "amn", if that is not found
it checks for a default value for top margin, if that is not found 
it checks for a default value for all margins.

process is repeated for all four margins, then all four border edges, then all four padding values.

meanings of the pseudo-attributes:

id = id
class = class
bgc = background-color
txc = color
bdc = border-color
fts = fontsize
flh = "float hidden" used to create a hidden div for use as a tooltip or jquery popup
ilh = "inline hidden" used to create a hidden div inline
wid = width. a numerical value for the container, and a fraction for all other divs
cmn = "center margin" gives a horizontal auto-width to center an element.
amn = "all margin" sets each side the same
abd ="all border" all sides of border the same
apd = "all padding all sides padding the same.

individual sides use the same naming convention:
t=top, r=right, b=bottom, l=left.

example tmn = topmargin, lbd=left border, bpd=bottom padding etc.



