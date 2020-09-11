<?php

// 특정 숙소의 요금 정보 탭을 가져온다.
function getMotelMoneyInfo($AccomIdx)
{

    $res = array();
    $res['PartTime'] = getMotelPartTimeInfo($AccomIdx);
    $res['AllDay'] = getMotelAllDayInfo($AccomIdx);

    return $res;
}

// 특정 숙소의 요금 정보 탭 중 대실 파트
function getMotelPartTimeInfo($AccomIdx)
{
    $pdo = pdoSqlConnect();
    $query = "
select PartTimePrice.RoomIdx      as PartTime_RoomIdx,
RoomName                   as PartTime_RoonmName,
WeekdayTime                as PartTime_Weekday_Time,
WeekendTime                as PartTime_Weekend_Time,
WeekdayDeadline            as PartTime_Weekday_Deadline,
WeekendDeadline            as PartTime_Weekend_Deadline,
MemberWeekdayTime          as PartTime_Member_Weekday_Time,
MemberWeekendTime          as PartTime_Member_Weekend_Time,
MemberWeekdayDeadline      as PartTime_Member_Weekday_Deadline,
MemberWeekendDeadline      as PartTime_Member_Weekend_Deadline,
PartTimeWeekdayPrice       as PartTime_Weekday_Price,
PartTimeWeekendPrice       as PartTime_Weekend_Price,
MemberPartTimeWeekdayPrice as PartTime_Member_Weekday_Price,
MemberPartTimeWeekendPrice as PartTime_Member_Weekend_Price
from PartTimeInfo
join PartTimePrice on PartTimeInfo.AccomIdx = PartTimePrice.AccomIdx
join Room on PartTimeInfo.AccomIdx = Room.AccomIdx and PartTimePrice.RoomIdx = Room.RoomIdx
where PartTimeInfo.AccomIdx = ?;
";

    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 특정 숙소의 요금 정보 탭 중 숙박 파트
function getMotelAllDayInfo($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "
select Room.RoomIdx             as AllDay_RoomIdx
, RoomName                 as AllDay_RoomName
, WeekdayTime              as AllDay_Weekday_Time
, WeekendTime              as AllDay_Weekend_Time
, WeekdayDeadline          as AllDay_Weekday_Deadline
, WeekendDeadline          as AllDay_Weekend_Deadline
, MemberWeekdayTime        as AllDay_Member_Weekday_Time
, MemberWeekendTime        as AllDay_Member_Weekend_Time
, MemberWeekdayDeadline    as AllDay_Member_Weekday_Deadline
, MemberWeekendDeadline    as AllDay_Member_Weekend_Deadline
, AllDayWeekdayPrice       as AllDay_Weekday_Price
, AllDayWeekendPrice       as AllDay_Weekend_Price
, MemberAllDayWeekdayPrice as AllDay_Member_Weekday_Price
, MemberAllDayWeekendPrice as AllDay_Member_Weekend_Price
from AllDayInfo
join AllDayPrice on AllDayInfo.AccomIdx = AllDayPrice.AccomIdx
join Room on AllDayInfo.AccomIdx = Room.AccomIdx and AllDayPrice.RoomIdx = Room.RoomIdx
where AllDayInfo.AccomIdx = ?;
";

    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 특정 숙소의 요금 정보 탭 중 숙박 파트
function getAccomSellerInfo($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "
select SellerName,
AccomIdx,
BusinessName,
BusinessAddress,
SellerEmail,
SellerContact,
SellerCode
from AccommodationSeller
where AccomIdx = ?;
";

    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// 특정 방의 대실 가격 정보 가져오기
function getPartTimePrice($AccomIdx, $RoomIdx, $isMember, $dayType)
{

    $pdo = pdoSqlConnect();
    $query = "
                select PartTimeWeekdayPrice,
                       PartTimeWeekendPrice,
                       MemberPartTimeWeekdayPrice,
                       MemberPartTimeWeekendPrice
                from PartTimePrice
                where AccomIdx = ?
                  and RoomIdx = ?;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx, $RoomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    /*
     * 평일,회원
     * 주말,회원
     * 평일,비회원
     * 주말,비회원
     */
    if(strcmp($dayType, 'weekday') == 0 && $isMember == true){
        return $res[0]['MemberPartTimeWeekdayPrice'];
    }
    //
    else if(strcmp($dayType, 'weekend') == 0 && $isMember == true){
        return $res[0]['MemberPartTimeWeekendPrice'];
    }
    else if(strcmp($dayType, 'weekday') == 0 && $isMember == false){
        return $res[0]['PartTimeWeekdayPrice'];
    }
    else
        return $res[0]['PartTimeWeekendPrice'];
}

// 특정 방의 숙박 가격 정보 가져오기
function getAllDayPrice($AccomIdx, $RoomIdx, $isMember, $dayType)
{

    $pdo = pdoSqlConnect();
    $query = "
                select WeekdayPrice, WeekendPrice, MemberWeekdayPrice, MemberWeekendPrice
                from RoomPrice
                where AccomIdx = ?
                and RoomIdx = ?;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx, $RoomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    /*
     * 평일,회원
     * 주말,회원
     * 평일,비회원
     * 주말,비회원
     */
    if(strcmp($dayType, 'weekday') == 0 && $isMember == true){
        return $res[0]['MemberWeekdayPrice'];
    }
    //
    else if(strcmp($dayType, 'weekend') == 0 && $isMember == true){
        return $res[0]['MemberWeekendPrice'];
    }
    else if(strcmp($dayType, 'weekday') == 0 && $isMember == false){
        return $res[0]['WeekdayPrice'];
    }
    else
        return $res[0]['WeekendPrice'];
}