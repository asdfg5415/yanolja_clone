<?php
// 특정 숙소의 대실 마감 시간 정보 가져오기
function getPartTimeDeadline($AccomIdx, $dayType)
{

    $pdo = pdoSqlConnect();
    $query = "
                select WeekdayDeadline, WeekendDeadline
                from PartTimeInfo
                where AccomIdx = ?;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    // 평일이면
    if(strcmp($dayType, 'weekday') == 0){
        return $res[0]['WeekdayDeadline'];
    }
    else
        return $res[0]['WeekendDeadline'];
}

// 특정 슥소의 대실 이용 시간 정보 가져오기
function getPartTimeHour($AccomIdx, $isMember, $dayType)
{

    $pdo = pdoSqlConnect();
    $query = "
                select WeekdayTime, WeekendTime, MemberWeekdayTime, MemberWeekendTime
                from PartTimeInfo
                where AccomIdx = ?
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
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
        return $res[0]['MemberWeekdayTime'];
    }
    //
    else if(strcmp($dayType, 'weekend') == 0 && $isMember == true){
        return $res[0]['MemberWeekendTime'];
    }
    else if(strcmp($dayType, 'weekday') == 0 && $isMember == false){
        return $res[0]['WeekdayTime'];
    }
    else
        return $res[0]['WeekendTime'];
}

// 특정 방의 숙박 이용 시작 시간 가져오기
function getAllDayTime($AccomIdx, $isMember, $dayType)
{

    $pdo = pdoSqlConnect();
    $query = "
                select WeekdayTime, WeekendTime, MemberWeekdayTime, MemberWeekendTime
                from AllDayInfo
                where AccomIdx = ?;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
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
        return $res[0]['MemberWeekdayTime'];
    }
    //
    else if(strcmp($dayType, 'weekend') == 0 && $isMember == true){
        return $res[0]['MemberWeekendTime'];
    }
    else if(strcmp($dayType, 'weekday') == 0 && $isMember == false){
        return $res[0]['WeekdayTime'];
    }
    else
        return $res[0]['WeekendTime'];
}