<?php

// key를 안 갖고 있으면 그냥 프로그램 종료
function isValidKey($key, $arr)
{
if (!array_key_exists($key, $arr)) {
$res = (object)array();
$res->isSuccess = false;
$res->code = 350;
$res->message = $key . ' 전달 없음';
echo json_encode($res, JSON_NUMERIC_CHECK);
exit;
}
}

// motelGroupIdx 의 유효성 검사
function isValidMotelGroupIdx($motelGroupIdx)
{
$pdo = pdoSqlConnect();
$query = "
select exists(select *
from MotelGroupName
where MotelGroupIdx = ?) as exist
";

$st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
$st->execute([$motelGroupIdx]);
$st->setFetchMode(PDO::FETCH_ASSOC);
$res = $st->fetchAll();

$st = null;
$pdo = null;

return $res[0]['exist'];
}

// hotelGroupIdx 의 유효성 검사
function isValidHotelGroupIdx($hotelGroupIdx)
{
$pdo = pdoSqlConnect();
$query = "
select exists(select *
from HotelGroupName
where HotelGroupIdx = ?) as exist
";

$st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
$st->execute([$hotelGroupIdx]);
$st->setFetchMode(PDO::FETCH_ASSOC);
$res = $st->fetchAll();

$st = null;
$pdo = null;

return $res[0]['exist'];
}

// AccomIdx 의 유효성 검사
function isValidAccomIdx($accomIdx)
{
$pdo = pdoSqlConnect();
$query = "
select exists(select *
from Accommodation
where AccomIdx = ?) as exist
";

$st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
$st->execute([$accomIdx]);
$st->setFetchMode(PDO::FETCH_ASSOC);
$res = $st->fetchAll();

$st = null;
$pdo = null;

return $res[0]['exist'];
}

// roomIdx 의 유효성 검사
function isValidRoomIdx($accomIdx, $roomIdx)
{
$pdo = pdoSqlConnect();
$query = "
select exists(select *
from Room
where AccomIdx = ? and RoomIdx = ?) as exist
";

$st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
$st->execute([$accomIdx, $roomIdx]);
$st->setFetchMode(PDO::FETCH_ASSOC);
$res = $st->fetchAll();

$st = null;
$pdo = null;

return $res[0]['exist'];
}

