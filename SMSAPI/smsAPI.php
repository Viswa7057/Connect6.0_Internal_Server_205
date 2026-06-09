<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); */

require_once("db_connect.php");
header("Content-Type: application/json");

// ============================
// Validate Request
// ============================

if (!isset($_REQUEST["mainid"]) || !isset($_REQUEST["phone_number"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing parameters"
    ]);
    exit;
}

$mainId     = mysqli_real_escape_string($conn, $_REQUEST["mainid"]);
$mobile = $_REQUEST["phone_number"];

// ============================
// Fetch KYC Links
// ============================

$query  = "SELECT `kyc_android`, `kyc_ios` FROM `call_logs` WHERE `mainid`='$mainId'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid ID or no data found"
    ]);
    exit;
}

$row = mysqli_fetch_assoc($result);

$kyc_android = $row['kyc_android'];
$kyc_ios     = $row['kyc_ios'];

// ============================
// Prepare SMS Text
// ============================

$textmessage = "Please click on the below link to complete the Cashe KYC.\n"
             . "Android: $kyc_android\n"
             . "iOS: $kyc_ios";

// ============================
// Prepare API Payload
// ============================

$postfields = array(
    'text' => $textmessage,
    'port' => array(18),
    'param' => array(
        array(
            'number' => $mobile,
            'user_id' => 1
        )
    )
);

// ============================
// CURL API Call
// ============================

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://192.168.3.231/api/send_sms',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($postfields),
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Digest username="admin", realm="Web Server", nonce="db16eaa1b0bab884a44df0ff7953e498", uri="/api/send_sms", algorithm="MD5", qop=auth, nc=00000001, cnonce="wzv8CAqh", response="0b16fc89ab3646a13fe9cca52b1ad9e8", opaque="5ccc069c403ebaf9f0171e9517f40e41"'
    ),
));

$response = curl_exec($curl);

if (curl_errno($curl)) {
    $error_msg = curl_error($curl);

    // Log failure
    mysqli_query($conn, "INSERT INTO sms_log (ref_id, mobile, message, api_response, status) 
        VALUES ('$mainId', '$mobile', '".mysqli_real_escape_string($conn,$textmessage)."', '".mysqli_real_escape_string($conn,$error_msg)."', 'error')");

    echo json_encode([
        "status" => "error",
        "message" => $error_msg
    ]);
    exit;
}

curl_close($curl);

// ============================
// Response Handling
// ============================

$result = json_decode($response, true);

if (isset($result['error_code']) && $result['error_code'] == 202) {

    // Log success
    mysqli_query($conn, "INSERT INTO sms_log (ref_id, mobile, message, api_response, status) 
        VALUES ('$mainId', '$mobile', '".mysqli_real_escape_string($conn,$textmessage)."', '".mysqli_real_escape_string($conn,$response)."', 'success')");

    echo json_encode([
        "status" => "success",
        "message" => "Message Delivered",
        "mobile" => $mobile
    ]);

} else {

    // Log failure
    mysqli_query($conn, "INSERT INTO sms_log (ref_id, mobile, message, api_response, status) 
        VALUES ('$mainId', '$mobile', '".mysqli_real_escape_string($conn,$textmessage)."', '".mysqli_real_escape_string($conn,$response)."', 'failed')");

    echo json_encode([
        "status" => "failed",
        "message" => "Message Not Delivered",
        "api_response" => $result
    ]);
}
?>