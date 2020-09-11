<?php

/*
 * *******************************************************************************************
 */

// 평일/주말 판단 함수
function getDayType($date)
{
    $day = date('w', strtotime($date));

    if ($day > 0 && $day < 5)
        $dayType = 'weekday';
    else
        $dayType = 'weekend';

    return $dayType;
}

// 무슨 타입의 숙소인지 판단하는 함수
function getTypeOfAccom($AccomIdx)
{
    $pdo = pdoSqlConnect();
    $query = "
                select AccomType
                from Accommodation
                where AccomIdx = ?;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['AccomType'];
}

// 특정 방의 해당 날짜에 대실이 가능한지 체크한다.
function checkPartTimeReserve($AccomIdx, $RoomIdx, $startAt, $endAt){
    $pdo = pdoSqlConnect();
    $query = "
                select not exists(select *
                from Reservation
                where AccomIdx = ?
                and RoomIdx = ?
                and ReserveType = 'P'
                and CheckInDate > date(?)
                and CheckOutDate < date(?)) as exist
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx, $RoomIdx, $startAt, $endAt]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    // 연박손님이 있으면 대실 불가
    if(!checkLongDayReserve($AccomIdx, $RoomIdx, $startAt, $endAt))
        return false;

    return $res[0]['exist'];
}

// 특정 방의 해당 날짜에 숙박이 가능한지 체크한다.
function checkAllDayReserve($AccomIdx, $RoomIdx, $endAt){
    $pdo = pdoSqlConnect();
    $query = "
                select not exists(select *
                  from Reservation
                  where AccomIdx = ?
                    and RoomIdx = ?
                    and ReserveType = 'A'
                    and CheckInDate < date(?)
                    and CheckOutDate > date(?)) as exist
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx, $RoomIdx, $endAt, $endAt]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

// (대실 체크에 종속적)특정 방에 해당 일에 연박을 하고 있는 사람이 있는지 체크한다. => 합치는 것 필요
function checkLongDayReserve($AccomIdx, $RoomIdx, $startAt, $endAt){
    $pdo = pdoSqlConnect();
    $query = "
                select not exists(select *
                from Reservation
                where ReserveType = 'A'
                      and AccomIdx = ?
                and RoomIdx = ?
                and CheckInDate < ?
                and CheckOutDate > ?) as exist;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx, $RoomIdx, $startAt, $endAt]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

// (대실에서의 실제적인 연박 체크)특정 방에 해당 일에 연박을 하고 있는 사람이 있는지 체크한다. => 합치는 것 필요
function checkConsecutiveStayAvailable($AccomIdx, $RoomIdx, $startAt, $dayDiff){

    // 9 ~ 12 연박 가정하면

    $afterStartAt = date("Y-m-d", strtotime($startAt." +1 day")); // 10;


    for($i=0; $i < $dayDiff - 1 ; $i++){ // 2번 돌고

        // 1. 겹치는 숙박이 없어야 한다.
        $tmp_startAt = date("Y-m-d", strtotime($afterStartAt." +".$i."day")); // 10, 11
        $tmp_endAt = date("Y-m-d", strtotime($tmp_startAt." +".$i."day")); // 11, 12

        if(!checkAllDayReserve($AccomIdx, $RoomIdx, $tmp_endAt)){ // 11,12 숙박 검사
            // 예약이 있으면
            return false;
        }

        // 2. 중간 날짜에 대실이 없어야 한다. => 10~11 , 11~12 대실 X
        if( !empty(getPartTimeCheckInOutTime($AccomIdx, $RoomIdx, $tmp_startAt, $tmp_endAt)) ){

            // 중간 날짜들의 대실 정보를 불러올 수 있으면
            return false;
        }
    }

    if(!checkAllDayReserve($AccomIdx, $RoomIdx, $afterStartAt))    // 10일에 숙박이 있으면
        return false;

    return true;

}

// (호텔용) 연박체크하는 함수
function hotelLongStayChecker($AccomIdx, $RoomIdx, $startAt, $dayDiff)
{
    // 9~12 연박이면, 퇴실일 기준 10일 자정, 11일 자정, 12일 자정에 숙박이 없어야한다.
    $afterStartAt = date("Y-m-d", strtotime($startAt . " +1 day")); // 10;

    for ($i = 0; $i < $dayDiff; $i++) { // 0, 1, 2

        // 1. 연박 기간동안 예약이 있으면 안된다.
        $tmp_endAt = date("Y-m-d", strtotime($afterStartAt . " +" . $i . "day")); // 10, 11, 12

        if (!checkAllDayReserve($AccomIdx, $RoomIdx, $tmp_endAt)) { // 10, 11,12 숙박 검사
            return false;       // 예약이 있으면
        }
        return true;
    }
}
