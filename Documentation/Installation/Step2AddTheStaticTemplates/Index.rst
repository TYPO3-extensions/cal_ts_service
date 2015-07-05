.. _Step2AddTheStaticTemplates:

================================
Step 2: Add the Static Templates
================================

.. include:: ../../Includes.txt

In order for the Calendar Base TypoScript Service extension to connect Calendar Base to other tables, you will need to add the desired Static Template to your Extension Template Record you have for the Calendar Base extension (alternatively, they can be installed in your site's main Template Record). There are currently three options to choose from:

- tt_news connector
- tx_seminars connector
- tt_products connector
- wec_sermons connector
- mbl_newsevent connector
- Birthday connector

If you are using the tx_seminars (cal_ts_service) or the tt_products (cal_ts_service) you have to define the page with the single view in your TypoScript setup:

plugin.tx_cal_controller.display.tt_products.externalPlugin.singleViewPid = 123
plugin.tx_cal_controller.display.seminars.externalPlugin.singleViewPid = 456

Have a look at the ":ref:`TypoScriptReference`" chapter for more information.


