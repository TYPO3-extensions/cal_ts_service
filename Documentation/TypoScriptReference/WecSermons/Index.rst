.. _TypoScriptReferenceWecSermon:

====================================
wec_sermons
====================================

.. include:: ../../Includes.txt

This is the default wec_sermons configuration used to show sermons within the calendar.

plugin.tx\_cal\_controller.display.wec_sermons

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         event_select

   Data type
         SELECT

   Description
         This is the heart of the connection SQL. It will be used in conjunction with the view-where (findallWithinWhere, findAll and findWhere) to retrieve the tt_news records.
         
         *selectFields* = tx_wecsermons_sermons.\*, 1 AS allday

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
         tx_wecsermons_sermons


.. container:: table-row

   Property
         findAllWithinWhere

   Data type
         String

   Description
         Define the where clause for the views: day, week, month, year, list, rss.
         Use ###START### and ###END### as marker to be replaced during runtime with the timeframe

   Default
         ((tx_wecsermons_sermons.occurrence_date >=###START### AND tx_wecsermons_sermons.occurrence_date+3600<=###END###) OR (tx_wecsermons_sermons.occurrence_date+3600<=###END### AND tx_wecsermons_sermons.occurrence_date+3600>=###START###) OR (tx_wecsermons_sermons.occurrence_date+3600>=###END### AND tx_wecsermons_sermons.occurrence_date<=###START###))


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
         tx_wecsermons_sermons.uid = ###UID###


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
         tx_wecsermons_pi1[showUid]=###UID###&tx_wecsermons_pi1[recordType]=tx_wecsermons_sermons


.. container:: table-row

   Property
         startTimeField

   Data type
         String

   Description
         Define the field containing the timestamp for the start time

   Default
         occurrence_date


.. container:: table-row

   Property
         endTimeField

   Data type
         String

   Description
         Define the field containing the timestamp for the end time.If there is no according field, leave it empty and define a defaultLength

   Default
         occurrence_date


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
         - description = description
         - image = graphic


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
         blue_catheader


.. container:: table-row

   Property
         bodyStyle

   Data type
         String

   Description
         The body style class to be used.

   Default
         blue_catbody


.. container:: table-row

   Property
         legendDescription

   Data type
         String

   Description
         The title in the legend description for these records

   Default
         Sermons


.. container:: table-row

   Property
         search.searchEventFieldList

   Data type
         String, CSV

   Description
         The fields, which are allowed to be searched through

   Default
         tx_wecsermons_sermons.title,tx_wecsermons_sermons.description,tx_wecsermons_sermons.keywords


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
               	file.import.stdWrap.wrap = uploads/tx_wecsermons/| 
             }



.. ###### END~OF~TABLE ######

[tsref:plugin.tx\_cal\_controller.display.wec_sermons]

