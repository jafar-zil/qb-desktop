<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use QuickBooks_Utilities;
use QuickBooks_WebConnector_QWC;

class QbIntegrationController extends Controller
{
   public function download (Request $request){
        $name = $request->filename;
        $descrip = 'QuickBooks Desktop Integration with Online Check Writer';
        $appurl = url('/qbd-connector/qbwc');		// This *must* be httpS:// (path to your QuickBooks SOAP server)
        $appsupport = url('/'); 		// This *must* be httpS:// and the domain name must match the domain name above
        $qbtype = QUICKBOOKS_TYPE_QBFS;	// You can leave this as-is unless you're using QuickBooks POS
        $readonly = false; // No, we want to write data to QuickBooks
        $run_every_n_seconds = 60; // Run every 60 seconds (1 minute)
        $username = Str::uuid();		// This is the username you stored in 'quickbooks_user' table
        $fileid = QuickBooks_WebConnector_QWC::GUID();
        $ownerid = QuickBooks_WebConnector_QWC::GUID();
        $dsn = 'mysql://'.config('database.connections.qbdesktop.username').':'.config('database.connections.qbdesktop.password').'@'.config('database.connections.qbdesktop.host').':'.config('database.connections.qbdesktop.port').'/'.config('database.connections.qbdesktop.database').'';
        QuickBooks_Utilities::createUser($dsn, $username, $request->password);
        $QWC = new QuickBooks_WebConnector_QWC($name, $descrip, $appurl, $appsupport, $username, $fileid, $ownerid, $qbtype, $readonly, $run_every_n_seconds);
        $xml = $QWC->generate();
        return response($xml);
       
   }
}
