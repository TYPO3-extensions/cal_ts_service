.. _HowItWorks:

=======================================
How the Cal Base TS Service Works
=======================================

.. include:: ../Includes.txt

This section describes how the Calendar Base TypoScript Service works and how the core extension (Calendar Base) interacts with the different services. It is necessary that the reader has knowledge about the normal TYPO3 extension structure and the Calendar Base extension.

When Calendar Base searches for events to be displayed, it looks for services, with the type “cal_event_model” and the subtype “event”. First of all it will find the “tx_cal_phpicalendar” service, which is the base service for returning the standard (tx_cal_event) events. It will continue to search other services and add any events that are found. The Calendar Base TypoScript Service offers a new service: “tx_cal_ts_service” to find events. Let's have a deeper look into this service.

The tx_cal_ts_service uses only TypoScript for configuration. The base configuration path is: “plugin.tx_cal_controller.display”. Underneath this path you define an unique id, for example the table you want to records from: “tt_news” and “tt_products”

.. code-block:: html

	plugin.tx_cal_controller {
	  display {
	    tt_news {
      		//insert configuration here
	    }
		tt_products {
		    //insert configuration here
	    }
	    ....
	  }
	}


The service runs through each path and tries to find events. “event_select” and “event_select_with_cat” are TypoScript SELECT objects, which get completed with “pidTable” and where-clause according to the view (“findAllWithinWhere” or “findWhere”). Using the startTimeField and  endTimeField the service knows where to look for the starting time and ending time of the event. If there is no ending time, you can define a default length in minutes.

To populate the standard event object with the values of the SQL result, you have to map event attributes to database fields. For example:

.. code-block:: html

	title = mytitle
	description = description
	image = image


If you don't want Calendar Base to handle the display of your event, you can also point to the single view of your own plugin: “externalPlugin = 1”. Define a “singleViewPid” and the “additionalParams” needed by your plugin. In “additionalParams” you can use ###DB_FIELD### to retrieve record related informations from the database (DB_FIELD is a placeholder).

