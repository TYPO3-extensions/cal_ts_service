.. _TypoScriptReferenceTtProducts:

====================================
tt_products
====================================

.. include:: ../../Includes.txt

This is the default tt_products configuration used to show products within the calendar.

plugin.tx\_cal\_controller.display.tt_products

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         event_select

   Data type
         SELECT

   Description
         This is the heart of the connection SQL. It will be used in conjunction with the view-where (findallWithinWhere, findAll and findWhere) to retrieve the tt_news records.
         
         *selectFields* = tt_products.\*

   Default
         See description


.. container:: table-row

   Property
         pidTable

   Data type
         String

   Description
         Configure the table the records come from.

   Default
         tt_products


.. container:: table-row

   Property
         findAllWithinWhere

   Data type
         String

   Description
         Define the where clause for the views: day, week, month, year, list, rss.
         Use ###START### and ###END### as marker to be replaced during runtime with the timeframe

   Default
         ((tt_products.sellstarttime>=###START### AND tt_products.sellstarttime<###END###) OR (tt_products.sellendtime<###END### AND tt_products.sellendtime>###START###) OR (tt_products.sellendtime>###END### AND tt_products.sellstarttime<###START###))


.. container:: table-row

   Property
         findAll

   Data type
         String

   Description
         Define the where clause for the ? view
         Use ###START### and ###END### as marker to be replaced during runtime with the timeframe

         Currently not in use!!

   Default
         -


.. container:: table-row

   Property
         findWhere

   Data type
         String

   Description
         Define the where clause for the single event views: event, ics
         Use ###UID### as marker to be replaced during runtime to define the uid of the record

   Default
         tt_products.uid = ###UID###


.. container:: table-row

   Property
         externalPlugin

   Data type
         Boolean

   Description
         Enable this to create a link to another page containing the single view of the external plugin.
         Use ###DB_FIELD### to retrieve record related informations from the database (DB_FIELD is a placeholder)

   Default
         0


.. container:: table-row

   Property
         externalPlugin.singleViewPid

   Data type
         Integer / PID

   Description
         Define the pid for the external plugin single view

   Default
         -


.. container:: table-row

   Property
         externalPlugin.additionalParams

   Data type
         String / stdWrap

   Description
         Contains the paramter(s) to be added to the url. Use ###DB_FIELD### to retrieve record related informations from the database (DB_FIELD is a placeholder)

   Default
         tx_ttproducts_pi1[product]=###UID###


.. container:: table-row

   Property
         startTimeField

   Data type
         String

   Description
         Define the field containing the timestamp for the start time

   Default
         sellstarttime


.. container:: table-row

   Property
         endTimeField

   Data type
         String

   Description
         Define the field containing the timestamp for the end time.If there is no according field, leave it empty and define a defaultLength

   Default
         sellendtime


.. container:: table-row

   Property
         defaultLength

   Data type
         Integer

   Description
         If there is no end time, you can define a default length (in minutes)

   Default
         0


.. container:: table-row

   Property
         fieldMapping

   Data type
         Array

   Description
         For a quick and easy integration, map your record fields to the standard event fields

   Default
         - title = title
         - description = note
         - image = image


.. container:: table-row

   Property
         template

   Data type
         String / Path

   Description
         The template to be used with these records

   Default
         EXT:cal_ts_service/Resources/Private/Templates/ts.tmpl


.. container:: table-row

   Property
         headerStyle

   Data type
         String

   Description
         The header style class to be used.

   Default
         green_catheader


.. container:: table-row

   Property
         bodyStyle

   Data type
         String

   Description
         The body style class to be used.

   Default
         green_catbody


.. container:: table-row

   Property
         legendDescription

   Data type
         String

   Description
         The title in the legend description for these records

   Default
         Angebote


.. container:: table-row

   Property
         search.searchEventFieldList

   Data type
         String, CSV

   Description
         The fields, which are allowed to be searched through

   Default
         tt_products.title,tt_products.note


.. container:: table-row

   Property
         event.event.image

   Data type
         cObject

   Description
         The image definition

   Default
         .. code-block:: html

             image >
             image = IMAGE
             image {
               	file.import.field = image
               	file.import.stdWrap.wrap = uploads/pics/| 
             }



.. ###### END~OF~TABLE ######

[tsref:plugin.tx\_cal\_controller.display.tt_products]

