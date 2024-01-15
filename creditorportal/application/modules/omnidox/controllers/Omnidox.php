<?php header('Access-Control-Allow-Origin: *');
if (!defined('BASEPATH')) exit('No direct script access allowed');
// session_start(); //we need to call PHP's session object to access it through CI
class Omnidox extends CI_Controller
{
    private $documents;

    private $successFulUploads = 0;

    function __construct()
    {
        parent::__construct();
    }

    function syncdocuments()
    {
        $this->documents = $this->fetchFailedDocuments();

        foreach ($this->documents as $document) {
            $documentUrl = $document->document_url;
            $documentUrlSegments = explode('/', $documentUrl);
            $fileName = end($documentUrlSegments);
            $requestJson = $this->prepareRequestArray($document->document_url, $document->lead_id, $fileName);
            if ($requestJson) {
                $this->updateDocumentStatus($this->callOmniDoxService($requestJson), $document);
            }
        }

        echo json_encode(['Total Documents' => count($this->documents), "Successful Uploads" => $this->successFulUploads, "Failed Uploads" => count($this->documents) - $this->successFulUploads]);
    }

    private function updateDocumentStatus($omniResponse, $document)
    {
        // If response is not successful we wont update
        if (!$this->isOmniRequestSuccessful($omniResponse)) {
            return;
        }

        $this->db->where('proposal_document_id', $document->proposal_document_id);
        $this->db->update('proposal_payment_documents', ['omnidox_success' => 1]);
        $this->successFulUploads++;
    }

    private function isOmniRequestSuccessful($omniResponse)
    {
        $data = json_decode($omniResponse);

        return $data->UploadResponse[0]->Error[0]->Code === "0";
    }

    private function fetchFailedDocuments()
    {
        $result = $this->db->get_where('proposal_payment_documents', array('omnidox_success' => 0))->result();
        return $result;
    }

    private function prepareRequestArray($image_path, $lead_id, $fileName)
    {
        if (!file_exists($image_path)) {
            return;
        }
        $img = file_get_contents($image_path);

        $fields = '{"Identifier":"ByteArray","UploadRequest":[{"CategoryID":"1003","DataClassParam":[{"Value":"' . $lead_id . '","DocSearchParamId":"22"}],"Description":"","ReferenceID":"3100","FileName":"","DocumentID":"2224","ByteArray":"","SharedPath":""}],"SourceSystemName":"Axis"}';
        $result = json_decode($fields, true);

        $result['UploadRequest'][0]['ByteArray'] = base64_encode($img);
        $result['UploadRequest'][0]['FileName'] = $fileName;

        return $result;
    }

    private function callOmniDoxService($requestArray)
    {
        $this->db->insert("logs_docs", [
            "req" => json_encode($requestArray),
            "lead_id" => $requestArray['UploadRequest'][0]['DataClassParam'][0]['Value'],
            "type" => "OmniDocs"
        ]);

        $id = $this->db->insert_id();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://bizpre.adityabirlahealth.com/ABHICL_OmniDocs/Service1.svc/uploadRequest",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($requestArray),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "password: esb@axis@ABHI",
                "postman-token: a3f0ed2e-f9cc-f767-09ae-4c594e38d5f2",
                "username: esb_axis"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $this->db->where([
                "id" => $id
            ])->update("logs_docs", [
                "res" => json_encode($err),
                "type" => "OmniDocs"
            ]);
        } else {
            $this->db->where([
                "id" => $id
            ])->update("logs_docs", [
                "res" => json_encode($response),
                "type" => "OmniDocs"
            ]);
        }

        return $response;
    }
}
