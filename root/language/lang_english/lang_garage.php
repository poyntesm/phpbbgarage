<?php
/***************************************************************************
 *                              lang_garage.php [English]
 *                            -------------------
 *   begin                : Friday, 06 May 2005
 *   copyright            : (C) Esmond Poynton
 *   email                : esmond.poynton@gmail.com
 *   description          : Provides Vehicle Garage System For phpBB
 *
 *   $Id$
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

//ADMIN : Categories
$lang['Any_Model'] = 'Any Model';
$lang['Select_Model'] = 'Select Model';
$lang['Garage_Categories_Title'] = 'Garage Categories Control';
$lang['Garage_Categories_Explain'] = 'On this screen you can manage your categories: create, alter, delete.';
$lang['Category_Permissions'] = 'Category Permissions';
$lang['Category_Title'] = 'Category Title';
$lang['Category_Desc'] = 'Category Description';
$lang['New_category_created'] = 'New category has been created successfully';
$lang['Click_return_garage_category'] = 'Click %sHere%s to return to the Garage Categories Manager';
$lang['Category_updated'] = 'This category has been updated successfully';
$lang['Delete_Category'] = 'Delete Category';
$lang['Move_Delete_Category_Button'] = 'Move Modifications and delete this category';
$lang['Move_Delete_Category'] = '<b>Move all existing modifications in this category to which category?</b>';
$lang['Remove_Category'] = '<b>Category to remove:</b>';
$lang['Delete_Category_Explain'] = 'The form below will allow you to delete a category';
$lang['Category_deleted'] = 'This category has been deleted successfully';

//ADMIN : Model & Makes
$lang['Garage_Models_Title'] = 'Garage Model & Makes Control';
$lang['Garage_Models_Explain'] = 'On this screen you can manage your models & makes: add, modify, delete.';
$lang['Add_Make'] = 'Add New Make';
$lang['Add_Make_Button'] = 'Add Make';
$lang['Modify_Make'] = 'Modify Existing Make';
$lang['Modify_Make_Button'] = 'Modify Make';
$lang['Delete_Make'] = 'Delete Existing Make';
$lang['Delete_Make_Button'] = 'Delete Make';
$lang['Add_Model'] = 'Add New Model';
$lang['Add_Model_Button'] = 'Add Model';
$lang['Modify_Model'] = 'Modify Existing Model';
$lang['Modify_Model_Button'] = 'Modify Model';
$lang['Choose_Modify_Model_Button'] = 'Choose Model To Modify';
$lang['Delete_Model'] = 'Delete Existing Model';
$lang['Delete_Model_Button'] = 'Delete Model';
$lang['Choose_Delete_Model_Button'] = 'Choose Model To Delete';
$lang['Vehicle_Make'] = 'Vehicle Make';
$lang['Vehicle_Model'] = 'Vehicle Model';
$lang['Change_To'] = 'Change To';
$lang['New_Make_Created'] = 'New Make Created Successfully';
$lang['New_Business_Created'] = 'New Business Created Successfully';
$lang['Business_Deleted'] = 'Business Deleted Successfully';
$lang['New_Model_Created'] = 'New Model Created Successfully';
$lang['Make_Updated'] = 'Make Updated Successfully';
$lang['Business_Updated'] = 'Business Updated Successfully';
$lang['Model_Updated'] = 'Model Updated Successfully';
$lang['Make_Deleted'] = 'Make Deleted Successfully';
$lang['Model_Deleted'] = 'Model Deleted Successfully';
$lang['Click_Return_Garage_Makes'] = 'Click %sHere%s to return to the Garage Makes & Model Manager';
$lang['Click_Return_Garage_Business'] = 'Click %sHere%s to return to the Garage Business Manager';
$lang['No_Make_Specified'] = 'No Make Specified';
$lang['No_Model_Specified'] = 'No Model Specified';
$lang['No_Name_Specified'] = 'No Name Specified';

//ADMIN : Configuration
$lang['Garage_Config_Title'] = 'Garage Configuration Control';
$lang['Garage_Config_Explain'] = 'On this screen you can manage garage general configuration options.';
$lang['Garage_Config_Updated'] = 'Garage Configuration Updated.';
$lang['Garage_Config'] = 'General Garage Configuration';
$lang['Click_Return_Garage_Config'] = 'Click %sHere%s to return to the Garage Configuration Manager';
$lang['Menu_Selection'] = '<b>Main Menu Selection</b><br />This affects what links are show in the main menu. Using CTRL select/deselect items that you would like to appear in the main menu.';
$lang['Cars_Per_Page'] = '<b>Vehicles Per Page</b><br />This affects how many vehicles are shown at once while browsing the garage.';
$lang['Year_Start'] = '<b>Year Range Beginning</b><br />This is the earliest year you want to appear as a selection for a new vehicle. Format CCYY';
$lang['Year_End'] = '<b>Year Range Ending Offset</b><br />This is the amount of years offset from the current year for the latest year you want to appear as a selection for a new vehicle. If set to a positive integer the amount will be added to the current year, and if you set this to a negative integer the amount will be subtracted from the current year. Default is to offset positive 1 since model year numbers are usually one year ahead current year.';
$lang['Max_User_Cars'] = '<b>Maximum allowed vehicles per user</b>';
$lang['Enable_Html_Car'] = '<b>Allow HTML for Vehicle Comments?</b><br />This will allow HTML to be posted and executed';
$lang['Enable_Html_Mod'] = '<b>Allow HTML for Modification Comments?</b><br />This will allow HTML to be posted and executed';
$lang['Allow_Guestbooks'] = '<b>Enable Vehicle Guest Books?</b>';
$lang['Garage_Features'] = 'Garage Main Menu Features';
$lang['Enable_Featured_Vehicle'] = '<b>Enable Featured Vehicle?</b><br />This will hilite a particular vehicle directly on the front page of the Garage.';
$lang['Featured_Vehcile_ID'] = '<b>Featured Vehicle</b><br />Selecy the Vehicle To Feature, By ID, Random Or Top Vehicle From Block.';
$lang['Featured_Vehcile_Description'] = '<b>Featured Vehicle Description</b><br />Enter a quick description of the featured vehicle.';
$lang['Date_Format'] ='<b>Date/Time Format</b><br />Enter the format for the Date/Time strings. See <a href="http://www.php.net/manual/en/function.date.php">php.net</a> for possible variables.<br /><br /><b>DEFAULT</b>: m-j-y H:i';
$lang['Enable_Newest_Vehicle'] = '<b>Enable Newest Vehicles?</b><br />Allows the newest added vehicles to be shown on the main menu.';
$lang['Enable_Updated_Vehicle'] = '<b>Enable Updated Vehicles?</b><br />Allows the latest updated vehicles to be shown on the main menu.';
$lang['Enable_Newest_Modifications'] = '<b>Enable Newest Modifications?</b><br />Allows the newest added modifications to be shown on the main menu.';
$lang['Enable_Updated_Modifications'] = '<b>Enable Updated Modifications?</b><br />Allows the latest updated modifications to be shown on the main menu.';
$lang['Enable_Most_Modded'] = '<b>Enable Most Modded?</b><br />Allows the most modded vehicles to be shown on the main menu.';
$lang['Enable_Most_Spent'] = '<b>Enable Most Money Spent?</b><br />Allows the most money spent on vehicles to be shown on the main menu.';
$lang['Enable_Most_Viewed'] = '<b>Enable Most Viewed?</b><br />Allows the most viewed vehicles to be shown on the main menu.';
$lang['Max_Most_Viewed'] = 'Max. no vehicles to show:';
$lang['Max_Mod_Viewed'] = 'Max. no modifications to show:';
$lang['Max_Comment_Viewed'] = 'Max. no comments to show:';
$lang['Enable_Latest_Commented'] = '<b>Enable Latest Commented?</b><br />Allows the latest commented vehicles to be shown on the main menu.';
$lang['Max_Latest_Commented'] = 'Max. no comments to show:';
$lang['Latest_Updated_Vehicle_All_Pages'] = '<b>Enable Latest Updated Vehicles?</b><br />Allows the latest updated vehicles to be shown on the left column on all pages.';

//ADMIN : Image Features
$lang['Garage_Image_Features'] = 'Garage Image Configurations';
$lang['Allow_Image_Upload'] = '<b>Allow Image Uploads?</b><br />This will allow users to upload images to be displayed with their vehicles and modifications.';
$lang['Allow_Mod_Images'] = '<b>Allow Modification Images?</b><br />This will allow users to upload an image for each listed modification in addition to the Max Images Per Vehicle Gallery.';
$lang['Show_Mod_Images_In_Gallery'] = '<b>Show Modification Images in Gallery?</b><br />This will collectively show all the images for all the vehicle modifications. You may set a maximum number of images to show in the gallery to prevent the page from filling up with images.';
$lang['Allow_Remote_Images'] = '<b>Allow users to use remote URL images?</b><br />This will allow users to link to an image remotely for any images in the Garage.';
$lang['Remote_Timeout'] = '<b>Remote Timeout</b><br />Enter the amount of time in whole seconds you want as a maximum time to wait to retrieve remote images.<br /><br /><b>DEFAULT</b>: 60';
$lang['Max_Images_Per_Gallery'] = '<b>Max Images Per Vehicle Gallery</b><br />Enter the maximum number of images to allow in each vehicle gallery. This does not include the modification images.<br />Set to 0 to disable.<br />Set to at least 1 to allow vehicle hilite images.';
$lang['Max_Image_Size'] = '<b>Maximum allowed image kbytes</b><br />Format in kilobytes, like 1024 for 1MB.';
$lang['Max_Image_Resolution'] = '<b>Maximum allowed image resolution</b><br />Single pixel count for maximum length or width, like 800 for a max 800x800.';
$lang['Create_Thumbs_With'] = '<b>Create Thumbnails Using?</b><br />Using ImageMagick is recommended for producing better quality, smaller file size thumbnails. GD is usually compiled into PHP so it might be the easiest to use and still produces similar quality thumbnails.<br /><br /><b>NOTE:</b> The use of GD will rely on your forums General Configuration value for GD Version. Please ensure this value is properly configured.';
$lang['GD'] = 'GD1/GD2';
$lang['IM'] = 'ImageMagicK';
$lang['Path_To_Convert'] = '<b>Path to convert</b><i> (If using ImageMagick)</i><br />Set this to the path to your ImageMagicks command line tool "convert". You may execute the command "type convert" if you have shell access to your host if you do not know this path.<br /><br /><b>DEFAULT:</b> /usr/bin/convert<br /><br /><b>For Windows:</b> Use forward slashes "/" in the path, and if it contains any spaces enclose the path in double quotes "';
$lang['Convert_Options'] = '<b>Convert Options</b><i> (If using ImageMagick)</i><br />These are the command line options that will be passed to convert in making thumbnails.<br /><br /><b>DEFAULT:</b> -antialias +profile "*"<br /><b>WINDOWS DEFAULT:</b> -anitalias<br /><b>NOTE:</b> DO NOT include any scale, geometry, or resize options here.<br /><br /><b>For Windows:</b> Remember to remove the +profile from the options!';
$lang['Thumbnail_Resolution'] = '<b>Thumbnail resolution</b><br />Single pixel count for length or width, like 100 for a max 100x100. Aspect Ratio will be maintained where possible.';

//ADMIN : Tools
$lang['Garage_Tools_Title'] = 'Garage Tool Control';
$lang['Garage_Tools_Explain'] = 'On this screen you can run garage tools.';
$lang['Garage_Tools_Rebuild'] = 'Rebuild All Thumbnails';
$lang['Garage_Tools_Rebuild_All'] = '<b>Rebuild All Thumbnails</b><br />If you have recently changed the thumbnail resolution, or have broken thumbnail images use this tool to rebuild all thumbnails. Depending on the size of your garage this tool can take a few minutes to run and is CPU intensive!';
$lang['Garage_Tools_Create_Log'] = '<b>Create Detailed Log?</b><br />If you have would like the Garage to create a detailed log of the rebuild actions please enter a filename here.<br /><br />Note that any existing file with the same name will be overwritten!<br /><br />If you do not want a detailed log to be created leave this field blank.';
$lang['Garage_Tools_Orphaned_Title'] = 'Find/Remove Orphans';
$lang['Per_Cycle'] = 'Per Cycle';
$lang['Garage_Tools_Orphaned_Button'] = 'Search For Orphans';
$lang['Garage_Tools_Orphaned'] = '<b>Find/Remove Orphans</b><br />This tool is used to locate any abondanded files that the Garage had once created. These abandoned files could be a result of doing any manual work in the database, running the rebuild tool and it failing part way through, or after substantial upgrading to the Garage. Under normal circumstances there should be no orphaned files.<br /><br />The first step of this tool is just to search for files, no action will be taken unless you confirm the findings on the next step.';

//ADMIN : Moderation
$lang['Garage_Moderation_Title'] = 'Garage Moderation Control';
$lang['Garage_Moderation_Explain'] = 'On this screen you can moderation user vehicles.';
$lang['Delete_Vehicle_Id_Title'] = 'Delete Vehicle ID Entry';
$lang['Vehicle_Id_To_Delete'] = '<b>Vehicle ID to Delete</b><br />Enter the ID of the vehicle you wish to delete.<br /><b>NOTE:</b>This is not undo-able!';
$lang['Delete_Vehicle'] = 'Delete Vehicle';
$lang['Mods'] = 'Mods';
$lang['Year'] = 'Year';
$lang['Make'] = 'Make';
$lang['Model'] = 'Model';
$lang['Colour'] = 'Colour';
$lang['Total_Mods'] = 'Total Mods';
$lang['Owner'] = 'Owner';
$lang['Vehicles_In_Garage'] = 'Current Vehicles In Garage';
$lang['Edit_Vehicle'] = 'Edit Vehicle';
$lang['Edit_This_Vehicle'] = 'Edit This Vehicle';
$lang['Modify_Vehicle'] = 'Modify Vehicle';
$lang['List_Mods'] = 'List_Mods';
$lang['Garage_Moderate_Car_Title'] = 'Garage Vehicle Moderation Control';
$lang['Garage_Moderate_Car_Explain'] = 'On this screen you can moderation a single users vehicle.';
$lang['Garage_Moderate_Mod_Title'] = 'Garage Vehicle Moderation Control';
$lang['Garage_Moderate_Mod_Explain'] = 'On this screen you can moderation a single users vehicle modification.';
$lang['Make_ID'] = 'Make ID';
$lang['Model_ID'] = 'Model ID';
$lang['Mileage'] = 'Mileage';
$lang['Mileage_Units'] = 'Mileage Units';
$lang['Purchased_Price'] = 'Purchased Price';
$lang['Installation_Price'] = 'Installation Price';
$lang['Currency'] = 'Currency';
$lang['Comments'] = 'Comments';
$lang['Description'] = 'Description';
$lang['Guestbook'] = 'Guestbook';
$lang['Moderate_Vehicle_Gallery'] = 'Moderate Vehicle Gallery';
$lang['Vehicle_Updated'] = 'Vehicle Updated';
$lang['Vehicle_Deleted'] = 'Vehicle Deleted';
$lang['Modification_Deleted'] = 'Modification Deleted';
$lang['Click_Return_Vehicle_Moderation'] = 'Click %sHere%s to return to the Garage Vehicle Moderation';
$lang['Click_Return_Modification_Moderation'] = 'Click %sHere%s to return to the Garage Modification Moderation';
$lang['Modification'] = 'Modification';
$lang['Rating'] = 'Rating';
$lang['Cost'] = 'Cost';
$lang['Title'] = 'Title';
$lang['Edit_Mod'] = 'Edit Mod';
$lang['Delete_Mod'] = 'Delete Mod';
$lang['Modify_Mod'] = 'Modify Modification';
$lang['Edit_This_Modification'] = 'Edit This Modification';
$lang['Remove_Mod_Images'] = 'Remove Modifications Attached Image';

//MAIN : Main Page
$lang['Menu'] = 'Menu';
$lang['Main_Menu'] = 'Main Menu';
$lang['Browse_Garage'] = 'Browse Garage';
$lang['Search_Garage'] = 'Search Garage';
$lang['Create_Vehicle'] = 'Create Vehicle';
$lang['My_Vehicles'] = 'My Vehicles';
$lang['Welcome'] = 'Welcome To The Garage';
$lang['Welcome_Text'] = 'Get your vehicle(s) listed in the Garage Today, to share with the world what you drive and what toys and modifications you have. The Garage can be a useful tool for finding vehicles and mods and getting some valuable insight!';
$lang['Total_Vehicles'] = 'Total Vehicles';
$lang['Total_Modifications'] = 'Total Modifications';
$lang['Total_Comments'] = 'Total Comments';
$lang['Total_Views'] = 'Total Views';
$lang['Create_New_Vehicle'] = 'Create New Vehicle';
$lang['Vehicle_Info'] = 'Vehicle Info';
$lang['PM_Guestbook_Notifications'] = 'PM Guestbook Notifications?';
$lang['Updated'] = 'Updated';
$lang['Ascending_Order'] = 'Ascending Order';
$lang['Descending_Order'] = 'Descending Order';
$lang['Last_Updated'] = 'Last Updated';
$lang['Last_Created'] = 'Last Created';
$lang['Sorted_By'] = 'vehicles sorted by';
$lang['Insurance_Sorted_By'] = 'Premiums Sorted By';
$lang['Go'] = 'Go!';
$lang['In'] = 'In';
$lang['Search_By_Member'] = 'Search By Member Name';
$lang['Search_By_Vehicle'] = 'Search By Vehicle';
$lang['Member_Name'] = 'Member Name';

$lang['Click_Return_Garage'] = 'Click %sHere%s to return to the Garage';
$lang['Vehicle_Created'] = 'Vehicle Created Succesfully';
$lang['Latest_Updated'] = 'Latest Updated';
$lang['Created'] = 'Created';
$lang['Install'] = 'Install';
$lang['Total_Spent'] = 'Total Spent';
$lang['Vehicle'] = 'Vehicle';
$lang['View_Vehicle'] = 'View Vehicle';
$lang['Add_New_Modification'] = 'Add New Modification';
$lang['Add_New_Insurance_Premium'] = 'Add New Insurance Premium';
$lang['Add_Modification'] = 'Add Modification';
$lang['Modification_Created'] = 'Modification Created';
$lang['Manage_Vehicle_Gallery'] = 'Manage Vehicle Gallery';
$lang['Create_New_Mod'] = 'Create New Mod';

$lang['Image_Attachments'] = 'Image Attachments';
$lang['Image_Attach'] = 'You may attach an image.';
$lang['Maximum_Image_File_Size'] ='Maximum file size';
$lang['Maximum_Image_Resolution'] = 'Maximum resolution';
$lang['Enter_Image_Url'] = 'Enter a URL to an online image';
$lang['Add_New_Image'] = 'Add New Image';
$lang['Image_Upload_Too_Big_Vehicle_Created_No_Image'] = 'Uploaded Image Has A File Size That Is Greater Than Allowed<br />Vehicle Has Been Created But No Image Uploaded<br />Please Use Manage Vehicle Gallery To Upload A Smaller File';
$lang['Not_Allowed_File_Type_Vehicle_Created_No_Image'] ='Uploaded Image Has A File Type That Is Not Allowed<br />Vehicle Has Been Created But No Image Uploaded<br />Please Use Manage Vehicle Gallery To Upload A Different File Type';
$lang['Upload_Image_Size_Too_Big_Vehicle_Created_No_Image'] ='Uploaded Image Has A Resolution That Is Greater Than Allowed <br />Vehicle Has Been Created But No Image Uploaded<br />Please Use Manage Vehicle Gallery To Upload A Smaller Resolution File';
$lang['Modification_Updated'] = 'Modification Updated';

$lang['Manage_Vehicle_Gallery_Note'] = 'Note: Only this particular vehicle gallery images are maintained through this interface. You may add new images above or delete existing images below. You may select your vehicles hilite image below. Your modification images will not be shown on this page.';
$lang['Image'] = 'Image';
$lang['Remove_Image'] = 'Remove Image';
$lang['Remove'] = 'Remove From Pending Listings - Delete Item From Users Garage';
$lang['Reassign'] = 'Remove From Pending Listings - Delete & Reassign (SELECT ONLY ONE BUSINESS)';
$lang['Approve'] = 'Approve From Pending Listings';
$lang['Hilite_Image'] = 'Hilite Image';
$lang['Current_Hilite_Image'] = 'Current Hilite Image';
$lang['Set_Hilite_Image'] = 'Set Hilite Image';

$lang['Add_New_Quartermile_Time'] = 'Add New Quarter Mile Time' ;
$lang['Add_New_Rollingroad_Run'] = 'Add New Rolling Road Run';
$lang['Garage_Quartermile_Times'] = 'Garage QuarterMile Time';
$lang['Add_New_Time'] = 'Add New Time';
$lang['Rt_Explain'] = '<b>Reaction Time</b><br />Enter Your Reaction Time';
$lang['Sixty_Explain'] = '<b>60 Foot Time</b><br />Enter Your Sixty Foot Time';
$lang['Three_Explain'] = '<b>330 Foot Time</b><br />Enter Your Three & Thirty Foot Time';
$lang['Eight_Explain'] = '<b>1/8 Mile Time</b><br />Enter Your 1/8 Time';
$lang['Eightmph_Explain'] = '<b>1/8 Mile MPH</b><br />Enter Your 1/8 Speed';
$lang['Thou_Explain'] = '<b>1000 Foot Time</b><br />Enter Your Thousand Foot Time';
$lang['Quart_Explain'] = '<b>1/4 Mile Time</b><br />Enter  Your 1/4 Mile Time';
$lang['Quartmph_Explain'] = '<b>1/4 Mile MPH</b><br />Enter Your 1/4 Mile Speed';
$lang['Garage_Rollingroad_Runs'] = 'Garage Rolling Road Runs';
$lang['Add_New_Run'] = 'Add New Run';
$lang['Boost_Explain']  = '<b>Boost</b><br />Enter Your Boost';
$lang['Dyno_Center'] = '<b>Dyno Center</b><br />Enter Dyno Center Used';
$lang['Peakpoint_Explain'] = '<b>Peak Point</b><br />RPM @ Which Power Peaked';
$lang['Bhp_Explain'] = '<b>BHP</b><br />Enter Your BHP';
$lang['Torque_Explain'] = '<b>Torque</b><br />Enter Your Torque';
$lang['Nitrous_Explain'] = '<b>Nitrous</b><br />Enter Your Nitrous Shot';
$lang['Edit'] = 'Edit';
$lang['Edit_Time'] = 'Edit Time';
$lang['Edit_Run'] = 'Edit Run';
$lang['Add_Run'] = 'Edit Run';
$lang['Dynocenter'] = 'Dynocenter';
$lang['Bhp'] = 'BHP';
$lang['Bhp_Unit'] = 'BHP Type';
$lang['Torque'] = 'Torque';
$lang['Torque_Unit'] = 'Torque Type';
$lang['Boost'] = 'Boost';
$lang['Boost_Unit'] = 'Boost Type';
$lang['Nitrous'] = 'Nitrous';
$lang['Peakpoint'] = 'Peakpoint';
$lang['Installed_By'] = 'Installed By';
$lang['Installation_Rating'] = 'Installation Rating';
$lang['Product_Rating'] = 'Product Rating';
$lang['Purchased_From'] = 'Purchased From';
$lang['Link_To_RR'] = 'Link To A RollingRoad Session';
$lang['Not_Vehicle_Owner'] = 'Sorry But You Do Not Appear To Be The Vehicle Owner';
$lang['Click_return_garage'] = 'Click %sHere%s to return to the Garage';
$lang['Click_return_index'] = 'Click %sHere%s to return to the index';
$lang['Add_Premium'] = 'Add Insurance Premium';
$lang['Edit_Premium'] = 'Edit Insurance Premium';
$lang['Premium_Price'] = 'Cost Of Premium';
$lang['Insurance_Company'] = 'Insurance Company';
$lang['Cover_Type'] = 'Insurance Cover Type';
$lang['Too_Many_Vehicles'] = 'Your Garage Is Full...Sorry But You Can Not Fit Any More Vehicles In It';
$lang['Insurance_Summary'] = 'Insurance Review';
$lang['Business_Approved'] = 'Business Approved';
$lang['Business_Removed'] = 'Business Removed';
$lang['View_Guestbook'] = 'View / Sign My Guestbook';
$lang['Add_Comment'] = 'Add A New Comment';
$lang['Post_Comment'] = 'Post Comment';
$lang['Add_First_Comment'] = 'There Are Currently No Comments In This Guestbook.<br /> If Authorized A Box Will Be Below And You Can Be The First To Leave A Message!<br /> If You See No Box You Are Not Authorized To Leave A Comment';
$lang['Featured_Vehicle'] = 'Featured Vehicle';
$lang['Newest_Modifications'] = 'Newest Modifications';
$lang['Newest_Vehicles'] = 'Newest Vehicles';
$lang['Latest_Vehicle_Comments'] = 'Latest Vehicle Comments';
$lang['Last_Updated_Vehicles'] = 'Last Updated Vehicles';
$lang['Last_Updated_Modifications'] = 'Last Updated Modifications';
$lang['Most_Modified_Vehicle'] = 'Most Modified Vehicle';
$lang['Most_Viewed_Vehicle'] = 'Most Viewed Vehicle';
$lang['Most_Money_Spent'] = 'Most Money Spent';
$lang['Author'] = 'Author';
$lang['Posted_Date'] = 'Posted Date';
$lang['Powered_By_Garage'] = 'Powered By phpBB Garage';
$lang['Required'] = 'Required';
$lang['Not_Listed_Yet'] = 'Not Listed Yet? Click ';
$lang['Here'] = 'Here';
$lang['Add_New_Business'] = 'Add New Business';
$lang['Approve_Business'] = 'Approve Business';
$lang['Remove_Business'] = 'Remove Business';
$lang['Business_Name'] = 'Business Name';
$lang['Address'] = 'Address';
$lang['Telephone'] = 'Telephone No.';
$lang['Fax'] = 'Fax No.';
$lang['Website'] = 'Website';
$lang['Email'] = 'Email';
$lang['Opening_Hours'] = 'Opening Hours';
$lang['Type'] = 'Type';
$lang['Edit_Comment'] = 'Edit Comment';
$lang['Check_For_PM'] = 'Check this to receive a PM when someone signs your vehicle\'s guestbook.';
$lang['Manage'] = 'Manage';
$lang['Clear_Time'] = 'Clear Time';
$lang['Lowest_Premium'] = 'Lowest Premium';
$lang['Average_Premium'] = 'Average Premium';
$lang['Highest_Premium'] = 'Highest Premium';
$lang['Insurance_Premiums'] = 'Insurance Premiums';
$lang['Quarter_Mile_Times'] = 'Quarter Mile Times';
$lang['Approve_QM'] = 'Approve Time';
$lang['QM_Approved'] = 'Quarter Mile Time Approved';
$lang['Remove_QM'] = 'Remove Time';
$lang['Rolling_Road_Runs'] = 'Rolling Road Runs';
$lang['Gallery'] = 'Gallery';
$lang['Vehicle_Pictures'] = 'Vehicle Pictures';
$lang['Modification_Pictures'] = 'Modification Pictures';
$lang['Max_Mod_Images_Viewed'] = 'Max. no of images to show';
$lang['Business_Notice'] = '<b>Please Note : Business Details Still Need Approval Of A Moderator/Administrator</b><br /><br />You Will Be Able To Use The Business Immediately For Your Vehicle,<br />However It Will Not Be Listed In Any Review Page Till Approved<br />If You Create A Duplicate Busineess It Will Be Deleted';

$lang['Car_Rt'] = 'R/T';
$lang['Car_Sixty'] = '60 Foot';
$lang['Car_Three'] = '330 Foot';
$lang['Car_Eigth'] = '1/8 Mile';
$lang['Car_Eigthm'] = '1/8 MPH';
$lang['Car_Thou'] = '1000 FT';
$lang['Car_Quart'] = '1/4 Mile';
$lang['Car_Quartm'] = '1/4 MPH';
$lang['Insurance'] = 'Insurance';
$lang['Quartermile'] = 'Quartermile';
$lang['Rollingroad'] = 'Rollingroad';
$lang['List_Insurance'] = 'List Premiums';
$lang['List_Quartermile'] = 'List Times';
$lang['List_Rollingroad'] = 'List Runs';
$lang['Image_Deleted'] = 'Image Deleted';
$lang['Hilite_Set'] = 'Hilite Image Set';
$lang['Insurance_Deleted'] = 'Insurance Premium Deleted';
$lang['Quartermile_Deleted'] = 'Quartermile Deleted';
$lang['Rollingroad_Deleted'] = 'Rollingroad Deleted';
$lang['Insurance_Updated'] = 'Insurance Premium Updated';
$lang['Quartermile_Updated'] = 'Quartermile Updated';
$lang['Rollingroad_Updated'] = 'Rollingroad Updated';
$lang['Garage_Review'] = 'Garage Review';
$lang['Premium'] = 'Premium';
$lang['Mod_Price'] = 'Mod Price';
$lang['Price'] = 'Price';
$lang['Search_Insurance_By_Vehicle'] = 'Search Insurance Premiums By Vehicle';
$lang['Garage_Orphans_Title'] = 'Garage Orphan Locator';
$lang['Garage_Orphans_Explain'] = 'Below are all the orphaned files that were found.  An orphaned file is defined as a file that exists on your local drive that is no longer present in the database.<br />Please check all the applicable orphans you wish to delete.<br /><br /><b>This operation is not undo-able!  Once you choose to remove an orphan it is gone for good.</b>';
$lang['Garage_Orphans_Table_Title'] = 'Select Orphaned Files to Remove -- <a href="\'#\'" onClick=\'select_all();return false;\'><i>(Click to toggle all checkboxes below)</i></a>';
$lang['Remove_Selected_Orphans'] = 'Remove Selected Orphans';
$lang['Garage_Business_Title'] = 'Garage Business Control';
$lang['Garage_Business_Explain'] = 'On this screen you can manage your business\'s: create, edit, delete.';
$lang['Garage_Permissions_Title'] = 'Garage Access Permissions Control';
$lang['Garage_Permissions_Explain'] = '(Check box for access, uncheck to not allow access). \'Browse\' dictates whether a user will be able to view the garage. \'Interact\' dictates whether the user will be able to vote and leave comments in the Garage. \'Add\' dictates whether the user will be able to add,edit & delete vehicles and modifications to the Garage. \'Upload\' dictates whether the user will be able to upload images in the Garage.<br /><br /><b>NOTE:</b> Regardless of permission settings for Guests below they will not be allowed to add a new vehicle to the Garage and hence images as well. To Set Private Permissions Grant Private To A Permision And Save...Then Usergroups Will Appear. Now Select Group To Grant Permission To And Save.';
$lang['Permission_Access_Levels'] = 'Permission Access Levels';
$lang['Name'] = 'Name';
$lang['Browse'] = 'Browse';
$lang['Interact'] = 'Interact';
$lang['Add'] = 'Add';
$lang['Upload'] = 'Upload';
$lang['Select'] = 'Select';
$lang['Global_All_Masks'] = 'GLOBAL: All Current And Future Permissions - Overides All Settings Below';
$lang['Granular_Permissions'] = 'Or: Adjust Permissions As Below With User Level\'s And User Groups ';
$lang['Private_Permissions'] = 'Private Permissions: Select Usergroups To Grant Premissions To ';
$lang['All_Masks'] = 'Global';
$lang['Quartermile_Table'] = '1/4 Mile Table';
$lang['Search_User_Garage'] = 'Vehicle Garage Of';

//Since 0.1.0
$lang['Please_Rate'] = 'Please Rate';
$lang['Rate'] = 'Rate';
$lang['Not_Rated_Yet'] = 'Vehicle Not Rated Yet';
$lang['Update_Rating'] = 'Update Existing Rating';
$lang['Top_Quartermile_Runs'] = 'Top Quartermile Runs';
$lang['Top_Rated_Vehicles'] = 'Top Rated Vehicles';
$lang['Enable_Top_Quartermile'] = '<b>Enable Top Quartermiles?</b><br />Allows the top quartermile times to be shown on the main menu.';
$lang['Max_Top_Quartermile'] = 'Max. no times to show:';
$lang['Enable_Top_Rated'] = '<b>Enable Top Rated?</b><br />Allows the top rated vehicles to be shown on the main menu.';
$lang['Garage_Quartermile_Features'] = 'Garage Quartermile Features';
$lang['Garage_Business_Features'] = 'Garage Business Features';
$lang['Enable_Quartermile'] = '<b>Allow Quartermile Times?</b><br />This will allow users to enter quartermile times to be displayed with their vehicle.';
$lang['Require_Quartermile_Approval'] = '<b>Quartermile Times Need Approval?</b><br />This will make all times need approval from a moderator or administrator before appearing in the table.';
$lang['Require_Business_Approval'] = '<b>Business\'s Need Approval?</b><br />This will make all business\'s need approval from a moderator or administrator before appearing.';
$lang['New_Category_Title'] = 'Enter New Category Title';
$lang['Garage_Rating_Features'] = 'Garage Vehicle Rating Features';
$lang['Rating_Permanent'] = '<b>Ratings Permanent?</b><br />Allows you to set the inital rating as a permanent unchangable value.';
$lang['Rate_Permanent'] = 'You Have Already Rated';
$lang['Rating_Always_Updateable'] = '<b>Rating Always Updateable?</b><br />If ratings not permanent this allows you to set if a rating can be changed at anytime, or only if vehicle has been updated since last rating.';
$lang['Vehicle_Update_Required_For_Rate'] = 'Vehicle Update Required Before You Can Update Rating';

//Since 0.1.1
$lang['Approve_RR'] = 'Approve Run';
$lang['RR_Approved'] = 'Rollingroad Run Approved';
$lang['Remove_RR'] = 'Remove Run';
$lang['Garage_Rollingroad_Features'] = 'Garage Rollingroad Features';
$lang['Garage_Insurance_Features'] = 'Garage Insurance Features';
$lang['Garage_Mileage_Features'] = 'Garage Mileage Features';
$lang['Enable_Insurance'] = '<b>Allow Insurance Premiums?</b><br />This will allow users to enter insurance premiums to be displayed with their vehicle.';
$lang['Enable_Mileage'] = '<b>Allow Mileage Data?</b><br />This will allow users to enter mileage data to be displayed with their vehicle.';
$lang['Enable_Rollingroad'] = '<b>Allow Rollingroad Runs?</b><br />This will allow users to enter rollingroad runs to be displayed with their vehicle.';
$lang['Require_Rollingroad_Approval'] = '<b>Rollingroad Runs Need Approval?</b><br />This will make all runs need approval from a moderator or administrator before appearing in the table.';
$lang['Rollingroad_Table'] = 'Rollingroad Table';
$lang['Add_New_Tank'] = 'Add New Tank';
$lang['Enter_Mileage'] = '<b>Mileage</b>:<br/><span class="gensmall">Please Enter Either Know Fuel Economy Or Amount Used</span>';
$lang['Distance'] = '<b>Distance</b>:<br/><span class="gensmall">Please Enter Distance Covered</span>';
$lang['Or'] = 'Or';
$lang['Month'] = '<b>Month</b>:<br /><span class="gensmall">Please Select The Month</span> ';
$lang['Motorway'] = '<b>Motorway</b>:<br /><span class="gensmall">Select The % Time Driving Was Motorway</span>';
$lang['Journey_Time'] = '<b>Average Journey Time</b>:<br /><span class="gensmall">Select average time per journey</span>';
$lang['Traffic'] = '<b>Traffic</b>:<br /><span class="gensmall">Select The % Time Driving Was In Traffic</span>';
$lang['Profile_Integration'] = '<b>Profile Image Integration</b>:<br /><span class="gensmall">Display thumbnails for all vehicle images rather than hilite image</span>';
$lang['Replace_With_New_Image'] = 'Replace With New Uploaded Image';
$lang['Replace_With_New_Remote_Image'] = 'Replace With New Remote Image';
$lang['Retail_Shop'] = 'Retail Shop';
$lang['Web_Shop'] = 'Web Shop';
$lang['Keep_Current_Image'] = 'Keep Current Image';
$lang['Quartermile_Pending'] = 'Quartermile Pending Times';
$lang['Rollingroad_Pending'] = 'Rollingroad Pending Runs ';
$lang['Make_Pending'] = 'Makes Pending';
$lang['Model_Pending'] = 'Models Pending';
$lang['Hilite_Image'] = 'Hilite Image';
$lang['Total_Views'] = 'Total Views';
$lang['Manage_Vehicle_Links'] = 'Manage Vehicle Links';
$lang['Showing'] = 'Showing';
$lang['Of'] = 'Of';
$lang['Images'] = 'Images';
$lang['Delete_Business'] = 'Delete Business';
$lang['Edit_Existing_Business'] = 'Edit Existing Business';
$lang['Edit_Business'] = 'Edit Business';
$lang['Garage'] = 'Garage';
$lang['Business_Type'] = 'Business Type';
$lang['Business_Opening_Hours'] = 'Business Opening Hours';
$lang['Business_Email'] = 'Business Email';
$lang['Business_Fax_No'] = 'Business Fax No.';
$lang['Business_Telephone_No'] = 'Business Telephone No.';
$lang['Business_Address'] = 'Business Address';
$lang['Business_Website'] = 'Business Website';
$lang['Or_Random'] = 'OR Random';
$lang['Or_Top_Vehicle_In'] = 'OR Top Vehicle In';
$lang['Administrators'] = 'Administrators';
$lang['Moderators'] = 'Moderators';
$lang['Registered_Users'] = 'Registered Users';
$lang['Guest_Users'] = 'Guests';
$lang['Private'] = 'Private';
$lang['Save'] = 'Save';
$lang['Set_Main_Vehicle'] = 'Set Main Vehicle';
$lang['Search_Results_For_Member'] = 'Search Results for Member Name ';
$lang['Search_Results_For_Make'] = 'Search Results for Make ';
$lang['Search_Results_For_Model'] = 'Search Results for Model ';
$lang['Last_Customers'] = 'Latest Customers To Use This Business';
$lang['Click_For_More_Detail'] = 'Click For More Detail';
$lang['Install_Comments'] = 'Install Comments';
$lang['Only_Show_In_Review'] = '[Will Only Show In Business Review]';
$lang['Business_Pending'] = 'Business Pending';
$lang['Miles'] = 'Miles';
$lang['Kilometers'] = 'Kilometers';
$lang['Wheel'] = 'wheel';
$lang['Hub'] = 'hub';
$lang['Flywheel'] = 'flywheel';
$lang['Third_Party'] = 'Third Party';
$lang['Third_Party_Fire_Theft'] = 'Thired Party, Fire & Theft';
$lang['Comprehensive'] = 'Comprehensive';
$lang['Comprehensive_Classic'] = 'Comprehensive - Classic';
$lang['Comprehensive_Reduced'] = 'Comprehensive - Reduced Mileage';
$lang['Select_A_Option'] = 'Please Select';
$lang['Select_A_Business'] = 'Select A Business';
$lang['No_Nitrous'] = 'No Nitrous';
$lang['25_BHP_Shot'] = '25 BHP Shot';
$lang['50_BHP_Shot'] = '50 BHP Shot';
$lang['75_BHP_Shot'] = '75 BHP Shot';
$lang['100_BHP_Shot'] = '100 BHP Shot';
$lang['Confirm_Delete_Vehicle'] = 'Are you sure you want to delete this vehicle and all modifications belonging to it?  This operation is not undo-able!';
$lang['Confirm_Delete_Modification'] = 'Are you sure you want to delete this modification?  This operation is not undo-able!';
$lang['Confirm_Delete_Premium'] = 'Are you sure you want to delete this insurance premium?  This operation is not undo-able!';
$lang['Confirm_Delete_Quartermile'] = 'Are you sure you want to delete this quartermile time?  This operation is not undo-able!';
$lang['Confirm_Delete_Rollingroad'] = 'Are you sure you want to delete this rolling road run?  This operation is not undo-able!';
$lang['Image_Added'] = 'Image Added To Vehicle Gallery';
$lang['Insurance_Results'] = 'Insurance Search Results';
$lang['Username_Results'] = 'Username Search Results';
$lang['Make_Results'] = 'Make Search Results';
$lang['Model_Results'] = 'Model Search Results';
$lang['Make_Model_Results'] = 'Make & Model Search Results';
$lang['Image_Attached'] = 'Image Available';
$lang['Guestbook_Notify_Subject'] = 'I Have Left A Vehicle Comment For You';
$lang['Guestbook_Notify_Text'] = '<b>****This message is automatically generated by the site.****</b><br /><br />Your Vehicle Has Recieved A Comment  Click %s To View Your Vehicle Guestbook';
$lang['Pending_Items'] = 'Items Requiring Approval';
$lang['Slip_Image_Attached'] = 'Slip Image Attached';
$lang['Veheicle_Image_Attached'] = 'Vehicle Image Attached';
$lang['Modification_Image_Attached'] = 'Modification Image Attached';
$lang['Enable_User_Submit_Make'] = '<b>Allow Users Submit Makes?</b><br />This will allow users to enter new makes into the DB.';
$lang['Enable_User_Submit_Model'] = '<b>Allow Users Submit Model?</b><br />This will allow users to enter new models into the DB.';
$lang['Shop_Review'] = 'Shop Review';
$lang['Quartermile_Speed_Unit'] = 'MPH'; 


//Added For RC2
$lang['Reassign_Business'] = 'Reassign Business';
$lang['Business_Deleted'] = 'Business Deleted : ';
$lang['Reassign_To'] = 'Business To Reassign Items To : ';
$lang['Reassign_Button'] = 'Reassign';
$lang['Translation_Link'] = '';

//Added For RC5
$lang['No_Orphaned_Files'] = 'You Do Not Appear To Have Any Orphaned Files';
$lang['Orphaned_Files_Removed'] = 'Orphaned Files Removed';
$lang['No_Orphaned_Files_Selected'] = 'No orphaned files were selected, therefore none were removed ;)';
$lang['Rebuild_Thumbnails_Complete'] = 'Rebuild All Thumbnails completed';
$lang['Permissions_Updated'] = 'Garage Permissions Updated.';
$lang['Shop'] = 'Shop';
$lang['Processing_Attach_ID'] = 'Processing attach_id: ';
$lang['Remote_Image'] = 'Remote Image: ';
$lang['File_Name'] = 'file_name: ';
$lang['Temp_File_Name'] = 'tmp_file_name: ';
$lang['Rebuilt'] = 'Rebuilt: ';
$lang['Thumb_File'] = 'Thumb File: ';
$lang['Source_File'] = 'Source File: ';
$lang['File_Does_Not_Exist'] = 'ERROR -- Remote file does not exist!';
$lang['Source_Unavailable'] = 'Rebuild Failed Source Image Unavailable: ';
$lang['No_Source_File'] = 'Thumb Creation Failed No Source File :';
$lang['Started_At'] = 'We started at : ';
$lang['Ended_At'] = 'We ended at : ';
$lang['Have_Done'] = 'We have done : ';
$lang['Need_To_Process'] = 'We need to process in total : ';
$lang['Log_To'] = 'We will log to : ';
$lang['Out_Of'] = 'Out Of';
$lang['Kbytes'] = 'kbytes';

//Added For New Category & Business Admin Pages
$lang['Deny'] = 'Deny';
$lang['Rename'] = 'Rename';
$lang['Empty_Title'] = 'You Have Not Entered A Category Title';
$lang['Category_Order_Updated'] = 'Category Order Updated';
$lang['Status'] = 'Status';
$lang['Show_Details'] = 'Show Details';
$lang['Hide_Details'] = 'Hide Details';
$lang['set_pending'] = 'Set To Pending';
$lang['set_approved'] = 'Set To Approved';
$lang['Delete_Business'] = 'Delete Category';
$lang['Move_Delete_Business_Button'] = 'Move Related Items And Delete Category';
$lang['Move_Delete_Business'] = '<b>Move all existing items related to this business to which existing business?</b>';
$lang['Remove_Business'] = '<b>Business to remove:</b>';
$lang['Missing_Required_Data'] = '<b>Missing Required Data</b>';
$lang['Delete_Business_Explain'] = 'The form below will allow you to delete a business. You need to decide which business related items will be moved to.';
$lang['Models'] = 'Models';
$lang['Reorder'] = 'Reorder';
$lang['Move_Up'] = 'Move Up';
$lang['Move_Down'] = 'Move Down';
$lang['Add_Quota'] = 'Vehicle Quota';
$lang['Upload_Quota'] = 'Upload Quota';
$lang['Garage_Enable_Images'] = '<b>Enable Garage Images</b><br />This affects what if buttons within the Garage are displayed with a image or as text.';


?>
