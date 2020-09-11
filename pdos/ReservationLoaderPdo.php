<?php

// 특정 방의 대실 예약의 체크인, 체크 아웃 시간 예약 정보를 가져온다.
function getPartTimeCheckInOutTime($AccomIdx, $RoomIdx, $startAt, $endAt)
{
    $pdo = pdoSqlConnect();
    $query = "
                select time(CheckInDate) as CheckIn, time(CheckOutDate) as CheckOut
                from Reservation
                where ReserveType = 'P'
                and      AccomIdx = ?
                and RoomIdx = ?
                and CheckInDate > date(?)
                and CheckOutDate < date(?)
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx, $RoomIdx, $startAt, $endAt]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 특정 방의 원하는 날짜 이전 날짜에 퇴실한 숙박 예약의 정보를 가져온다.
function getYesterdayAllDayReservation($AccomIdx, $RoomIdx, $beforeEndAt)
{
    // 쿼리의 ?는 퇴실시간 기준으로 나눠야함
    $pdo = pdoSqlConnect();
    $query = "
                select *
                from Reservation
                where ReserveType = 'A'
                  and AccomIdx = ?
                  and RoomIdx = ?
                  and CheckInDate < date(?)
                  and CheckOutDate > date(?);
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx, $RoomIdx, $beforeEndAt, $beforeEndAt]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 특정 방의 특정 날짜 당일의 숙박 예약의 정보를 가져온다.
function getTodayAllDayReservation($AccomIdx, $RoomIdx, $startAt, $endAt)
{

    $pdo = pdoSqlConnect();
    $query = "
                select *
                from Reservation
                where ReserveType = 'A'
                  and AccomIdx = ?
                  and RoomIdx = ?
                  and CheckInDate > ?
                  and CheckInDate < ?
                  and CheckOutDate > ?
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx, $RoomIdx, $startAt, $endAt, $endAt]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}
