DESCRIPTION

timestamp frontendfilter for the contao extension "MetaModels"
(https://github.com/MetaModels).

The filter provides the modes "datepicker" and "groups". 
The mode datepicker renders a inputfield with datepicker for each attribut (max. 2).
The mode "groups" renders a select-field and put all items in date groups.

example: 

lets say, you have a metamodel like this:

travel_name (text)
published (checkbox)
start_date (timestamp)
end_date (timestamp)

with this values:

super canada tour
20.01.2015
02.02.2015

super germany tour
01.02.2015
15.02.2015

super romania tour
01.03.2015
12.03.2015

The "group"-mode will render a selectfield with this options:

<select>
<option>January 2015</option>
<option>February 2015</option>
<option>March 2015</option>
</select>

on selection, all items in range of month/year will be shown.


INSTALLATION

- install MetaModel Version 2.
- install MetaModel timestamp attribut.
- copy the content of this package in system/modules
(e.g.: /var/www/myContaoProject/system/modules/filter_timestamp)


USEAGE

create a metamodel, attributes (at least one of type timestamp and checkbox) and filters.

1. chose filtertype "date filter"
2. chose one or two attributes (only types timestamp will appear)
3. define a url-parameter (no auto_item support). (e.g. "date")
4. chose a mode
5. for mode "groups" you have to define a date pattern (e.g. F Y)
6. all the other options a optional, play with it. 
7. create a filter "published state" for the checkbox attribute


BUGS
- without a published state filter the timestamp-filter is empty.




