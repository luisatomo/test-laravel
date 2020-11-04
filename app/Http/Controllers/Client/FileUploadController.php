<?php

namespace App\Http\Controllers\Client;

use App\Helpers\GdriveHelper;
use App\Helpers\StringHelper;
use App\Http\Controllers\AppBaseController;
use App\Models\CompanyFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends AppBaseController
{
    //
    // Constructor
    public function _construct(Request $request)
    {
        $disk = 'gcs';
        $path = 'files-'.$request->user()->id.'/';
    }

    public function Create()
    {
        return view('imageupload');
    }

    public function Store(Request $request)
    {
        if ($request->hasfile('file') &&
            $request->get('company') &&
            $request->get('companyId') &&
            $request->get('type')) {
            $month = $request->get('month');
            $year = $request->get('year');
            $companyName = StringHelper::cleanCompanyName($request->get('company'));
            $companyId = $request->get('companyId');
            $type = $request->get('type');

            // Find parent dir for reference
            $path = GdriveHelper::getDirectory($companyName);

            $folderPath = $path;

            $file = $request->file('file');
            $name = str_replace(' ', '-', $file->getClientOriginalName());
            $storagePath = Storage::putFileAs($folderPath, $file, $name);

            if ($storagePath) {
                $companyFile = new CompanyFiles();
                $companyFile->company_id = $companyId;
                $companyFile->url = $storagePath;
                $companyFile->company_files_type_id = $type;
                $companyFile->month = $month;
                $companyFile->name = $name;
                $companyFile->year = $year;
                $companyFile->save();

                $data['success'] = 'file has been uploaded';
                $data['url'] = $storagePath;
                $data['fileId'] = $companyFile->id;
            }

            return response()->json($data);
        }

        return response()->json(['error' => 'The request does not have a file or a company name']);
    }

    public function driveInit(Request $request)
    {
        if ($request->get('company')) {
            $companyName = StringHelper::cleanCompanyName($request->get('company'));
            $path = GdriveHelper::getDirectory($companyName);
            if ($path) {
                $folders = [
                    'VAR Sheets',
                    'UW Documents',
                    'Processing Statements',
                    'Old: Unused Documents',
                    'Bank Statements',
                    'Applications',
                    'Account Change Forms',
                ];
                foreach ($folders as $folder) {
                    GdriveHelper::getDirectory($folder, $path);
                }
            }

            return response()->json(['path' => $path]);
        }
    }

    public function Destroy(Request $request)
    {
        if ($request->get('filename') && $request->get('company')) {
            $companyName = StringHelper::cleanCompanyName($request->get('company'));
            $gDriveHelper = new GdriveHelper();
            $response = $gDriveHelper->delete('/'.$companyName, $request->get('filename'));
            CompanyFiles::destroy($request->get('fileid'));

            return response()->json(['success' => $response]);
        }
    }

    public function retrieveFile(Request $request)
    {
        $filename = $request->get('filename');
        $gDriveHelper = new GdriveHelper();

        return $gDriveHelper->getFile($filename);
        //return $data
    }

    public function renderFile(Request $request)
    {
        $filename = $request->get('filename');
        $gDriveHelper = new GdriveHelper();

        return $gDriveHelper->getFile($filename, false);
        //return $data
    }
}
