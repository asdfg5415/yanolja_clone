<?php
// 특정 그룹 검색 인원에 맞는 모든 모텔의 idx를 가져온다.
function getMotelAccomList($motelGroupIdx, $adult, $child)
{
    $pdo = pdoSqlConnect();
    $query = "
                select distinct Accommodation.AccomIdx
                from Region join Accommodation on Region.RegionIdx = Accommodation.RegionIdx
                join MotelGroup on MotelGroup.RegionIdx = Accommodation.RegionIdx
                join Room on Room.AccomIdx = Accommodation.AccomIdx
                where MotelGroupIdx = ?
                  and AccomType = 'M'
                and ? + ? <= MaxCapacity;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$motelGroupIdx, $adult, $child]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 특정 그룹 검색 인원에 맞는 모든 호텔의 idx를 가져온다.
function getHotelAccomList($hotelGroupIdx, $adult, $child)
{
    $pdo = pdoSqlConnect();
    $query = "
                select distinct Accommodation.AccomIdx
                from Accommodation
                         join HotelGroup on HotelGroup.RegionIdx = Accommodation.RegionIdx
                         join Room on Room.AccomIdx = Accommodation.AccomIdx
                where HotelGroupIdx = ?
                  and AccomType = 'H'
                  and ? + ? <= MaxCapacity
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$hotelGroupIdx, $adult, $child]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 특정 그룹 검색 인원에 맞는 <<모든 모텔>>의 방을 다 불러온다.
function getAllMotelRoomList($motelGroupIdx, $adult, $child)
{
    $pdo = pdoSqlConnect();
    $query = "
                select Accommodation.AccomIdx, RoomIdx
                from Region join Accommodation on Region.RegionIdx = Accommodation.RegionIdx
                join MotelGroup on MotelGroup.RegionIdx = Accommodation.RegionIdx
                join Room on Room.AccomIdx = Accommodation.AccomIdx
                where MotelGroupIdx = ?
                  and AccomType = 'M'
                and ? + ? <= MaxCapacity;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$motelGroupIdx, $adult, $child]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 특정 그룹 검색 인원에 맞는 <<특정 모텔>>의 방을 다 불러온다.
function getMotelRoomList($motelGroupIdx, $accomIdx,$adult, $child)
{
    $pdo = pdoSqlConnect();
    $query = "
                select Accommodation.AccomIdx, RoomIdx, RoomName
                from Region join Accommodation on Region.RegionIdx = Accommodation.RegionIdx
                join MotelGroup on MotelGroup.RegionIdx = Accommodation.RegionIdx
                join Room on Room.AccomIdx = Accommodation.AccomIdx
                where MotelGroupIdx = ?
                  and Accommodation.AccomIdx = ?
                  and AccomType = 'M'
                and ? + ? <= MaxCapacity;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$motelGroupIdx, $accomIdx, $adult, $child]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 특정 그룹 검색 인원에 맞는 <<모든 호텔>>의 방을 다 불러온다.
function getAllHotelRoomList($hotelGroupIdx, $adult, $child)
{
    $pdo = pdoSqlConnect();
    $query = "
                select Accommodation.AccomIdx, RoomIdx
                from Region
                         join Accommodation on Region.RegionIdx = Accommodation.RegionIdx
                         join HotelGroup on HotelGroup.RegionIdx = Accommodation.RegionIdx
                         join Room on Room.AccomIdx = Accommodation.AccomIdx
                where HotelGroupIdx = ?
                  and AccomType = 'H'
                  and ? + ? <= MaxCapacity;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$hotelGroupIdx, $adult, $child]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 특정 그룹 검색 인원에 맞는 <<특정 호텔>>의 방을 다 불러온다.
function getHotelRoomList($hotelGroupIdx, $accomIdx, $adult, $child)
{
    $pdo = pdoSqlConnect();
    $query = "
                select Accommodation.AccomIdx, RoomIdx
                from Region
                         join Accommodation on Region.RegionIdx = Accommodation.RegionIdx
                         join HotelGroup on HotelGroup.RegionIdx = Accommodation.RegionIdx
                         join Room on Room.AccomIdx = Accommodation.AccomIdx
                where HotelGroupIdx = ?
                  and Room.AccomIdx = ?
                  and AccomType = 'H'
                  and ? + ? <= MaxCapacity;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$hotelGroupIdx, $accomIdx, $adult, $child]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}


// 호텔의 지역그룹을 다 가져온다.
function getHotelGroupList()
{
    $pdo = pdoSqlConnect();
    $query = "  
                select distinct cityIdx, cityName, HotelGroup.HotelGroupIdx, HotelGroupName
                from Region
                         join HotelGroup on Region.RegionIdx = HotelGroup.RegionIdx
                         join HotelGroupName on HotelGroup.HotelGroupIdx = HotelGroupName.HotelGroupIdx
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 모텔의 지역그룹을 다 가져온다.
function getMotelGroupList()
{
    $pdo = pdoSqlConnect();
    $query = "  
                select distinct cityIdx, cityName, MotelGroupName.MotelGroupIdx, MotelGroupName
                from Region join MotelGroup on MotelGroup.RegionIdx = Region.RegionIdx
                        join MotelGroupName on MotelGroup.MotelGroupIdx = MotelGroupName.MotelGroupIdx
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// [Motel]
// 해당 지역의 조건에 맞는 모든 모텔 방들의 정보를 그룹핑한다.
function getMotels($isMember, $startAt, $endAt, $motelGroupIdx, $adult, $child)
{
    // 0.  결과배열 선언 및 초기화
    $motels = array();

    // 1.인원 조건에 맞는 모텔의 객실에 대한 정보를 가져온다.
    $motelRoomInfo = getAllMotelRoomsInfo($isMember, $startAt, $endAt, $motelGroupIdx, $adult, $child);

    // 2. 조건을 만족하는 객실의  총 개수 => 구성이 어떤지는 모름
    $numOfTotalRoom = count($motelRoomInfo);

    // 조건에 맞는 방이 하나도 없는 경우 => 빈 문자열 리턴
    if ($numOfTotalRoom == 0) return '';

    // 3. 조건을 만족하는 숙소의 AccomIdx 리스트, 개수
    $AccomList = getMotelAccomList($motelGroupIdx, $adult, $child);
    $numOfAccom = count($AccomList);

    // 4. 조건에 맞는 객실이 숙소마다 몇 개인지 파악한다. => 문자열에서 연속된 같은 숫자 세기
    // AccomList와 대응하는 방들의 개수가 채워진다. => AccomList의 n번째 Accomidx는 roomCount의 n번째 값만큼 방 개수를 가진다.
    $numOfRoomByAccom = array();
    $count = 1;

    // 1개면 루프를 못 돈다.
    if ($numOfAccom == 1) {
        $numOfRoomByAccom[0] = 1;
    } else {

        for ($i = 0; $i < $numOfTotalRoom - 1; $i++) { //9번 돌거임,8까지
            if ($motelRoomInfo[$i]['AccomIdx'] == $motelRoomInfo[$i + 1]['AccomIdx']) {
                // 뒤에 AccomIdx와 같은 경우
                $count++;
            } else {
                // 뒤에 AccomIdx문자와 다른 경우
                $numOfRoomByAccom[] = $count;
                $count = 1;
            }
            // 마지막엔 강제로 넣어준다.
            if ($i == $numOfTotalRoom - 2)
                $numOfRoomByAccom[] = $count;
        }
    }

    // 5. 각 숙소마다 그룹핑한다.
    $roomCount = 0;

    // 숙소 개수만큼 돈다.
    for ($i = 0; $i < $numOfAccom; $i++) {

        // 숙소당 처음 판단하는 값인지 판단.
        $isFirstForPartTime = true;
        $isFirstForAllDay = true;

        // AccomIdx 추가
        $motels[$i]['AccomIdx'] = $motelRoomInfo[$roomCount]['AccomIdx'];
        $motels[$i]['AccomName'] = getAccomInfo($motels[$i]['AccomIdx'])['AccomName'];
        $motels[$i]['AccomThumbnailUrl'] = getAccomInfo($motels[$i]['AccomIdx'])['AccomThumbnailUrl'];
        $motels[$i]['AvgRating'] = getAccomInfo($motels[$i]['AccomIdx'])['avgRating'];
        $motels[$i]['NumOfReview'] = getAccomInfo($motels[$i]['AccomIdx'])['numOfReview'];
        $motels[$i]['NumOfUserPick'] = getUserPick($motels[$i]['AccomIdx']);
        $motels[$i]['GuideFromStation'] = getAccomInfo($motels[$i]['AccomIdx'])['GuideFromStation'];

        // 숙소당 방 개수맘큼 돈다.
        for ($j = 0; $j < $numOfRoomByAccom[$i]; $j++) {

            /* * * * * * * * * * * * *
             *  1. 대실 가능여부 체크   *
             * * * * * * * * * * * * */

            // 1-1.대실이 가능한 경우
            if ($motelRoomInfo[$roomCount]['IsPartTimeAvailable'] == 'T') {

                // 첫 번째 대실 가능한 숙소의 경우 가격 비교 과정 X
                if ($isFirstForPartTime) {
                    // 첫 번째는 그냥 할당.
                    $isFirstForPartTime = false;
                    $motels[$i]['IsPartTimeAvailable'] = $motelRoomInfo[$roomCount]['IsPartTimeAvailable'];
                    $motels[$i]['PartTimePrice'] = $motelRoomInfo[$roomCount]['PartTimePrice'];
                    $motels[$i]['PartTimeHour'] = $motelRoomInfo[$roomCount]['PartTimeHour'];
                }

                // 두 번째 대실 가능한 숙소부터는 가격 비교 시작
                else {
                    // 새로운 대실비 < 기존의 대실비 ===> 결과 배열에 할당
                    if ($motelRoomInfo[$roomCount]['PartTimePrice'] < $motels[$i]['PartTimePrice']) {
                        $motels[$i]['PartTimePrice'] = $motelRoomInfo[$roomCount]['PartTimePrice'];
                        $motels[$i]['PartTimeHour'] = $motelRoomInfo[$roomCount]['PartTimeHour'];
                    }
                }
            }

            /* * * * * * * * * * * * *
             *  2. 숙박 가능여부 체크   *
             * * * * * * * * * * * * */

            // 2-1. 숙박이 가능한 경우
            if ($motelRoomInfo[$roomCount]['IsAllDayAvailable'] == 'T') {

                // 첫 번째 숙박 가능한 숙소의 경우 가격 비교 과정 X
                if ($isFirstForAllDay) {
                    // 첫 번째는 그냥 할당.
                    $isFirstForAllDay = false;
                    $motels[$i]['IsAllDayAvailable'] = $motelRoomInfo[$roomCount]['IsAllDayAvailable'];
                    $motels[$i]['AvailableAllDayCheckIn'] = $motelRoomInfo[$roomCount]['AvailableAllDayCheckIn'];
                    $motels[$i]['AllDayPrice'] = $motelRoomInfo[$roomCount]['AllDayPrice'];
                }
                // 두 번째 숙박 가능한 숙소부터는 가격 비교 시작
                else {
                    // 새로운 숙박비 < 기존의 숙박비 ===> 결과 배열에 할당
                    if ($motelRoomInfo[$roomCount]['AllDayPrice'] < $motels[$i]['AllDayPrice']) {
                        $motels[$i]['AvailableAllDayCheckIn'] = $motelRoomInfo[$roomCount]['AvailableAllDayCheckIn'];
                        $motels[$i]['AllDayPrice'] = $motelRoomInfo[$roomCount]['AllDayPrice'];
                    }
                }
            }

            // 다음 방 체크
            $roomCount++;
        }

        // 모두 대실 불가능 경우 ==> 판단이 한 번도 일어나지 않은 경우
        if ($isFirstForPartTime) {
            $motels[$i]['IsPartTimeAvailable'] = 'F';
        }

        // 모두 숙박 불가능 경우 ==> 판단이 한 번도 일어나지 않은 경우
        if ($isFirstForAllDay) {
            $motels[$i]['IsAllDayAvailable'] = 'F';
        }


        $accomTag = getAccomTag($motels[$i]['AccomIdx']);
        if(empty($accomTag)){
            $motels[$i]['AccomTag'] = array();
        }
        else{
            $motels[$i]['AccomTag'] = $accomTag;
        }



    }

    return $motels;
}

// [Motel]
// 해당 지역의 조건에 맞는 모든 모텔들 모든 방 정보 가져오기
function getAllMotelRoomsInfo($isMember, $startAt, $endAt, $motelGroupIdx, $adult, $child)
{

    // 전날 변수 저장
    $beforeStartAt = date("Y-m-d", strtotime($startAt." -1 day"));
    $beforeEndAt = date("Y-m-d", strtotime($endAt." -1 day"));


    // 평일,주말 판단
    $dayType = getDayType($startAt);

    // 숙박 이용 날짜 차이 구하기
    $dayDiff = (strtotime($endAt) - strtotime($startAt))/60/60/24;

    // 해당 지역 그룹의 모든 방 다 가져오기
    $motelRoomlist = getAllMotelRoomList($motelGroupIdx, $adult, $child);



    // 방 마다 돌면서 조건 체크
    for ($i = 0; $i < count($motelRoomlist); $i++) {

        $nowAccomIdx = $motelRoomlist[$i]['AccomIdx'];
        $nowRoomIdx = $motelRoomlist[$i]['RoomIdx'];

        // 1. 1박인 경우 => 숙박 + 대실만 가능
        if ($dayDiff == 1) {

            // 1-1.해당 객실의 대실이 가능하다면
            if (checkPartTimeReserve($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)) {

                $motelRoomlist[$i]['IsPartTimeAvailable'] = 'T';

                // 이전 날 숙박이 있었는지 체크 후 입실 가능 시간 배정
                if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $beforeEndAt)){
                    // 이전 날 숙박이 없었다면 => '10:00:00'
                    $motelRoomlist[$i]['AvailablePartTimeCheckIn'] = '10:00:00';
                }else{
                    // 이전 날 숙박이 있었다면 => (이전 숙박 퇴실 시간 + 1시간) 대실 입실 가능 시간
                    $motelRoomlist[$i]['AvailablePartTimeCheckIn'] =  date("H:i:s", strtotime(getYesterdayAllDayReservation($nowAccomIdx, $nowRoomIdx, $beforeEndAt)[0]['CheckOutDate']." +1hours"));
                }

                // 대실 당일 숙박예약이 있는지 체크 후 퇴실 가능 시간 배정
                if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $endAt)){
                    // 대실 당일 숙박 예약이 없는 경우 = > 대실 퇴실 시간 마감까지 가능
                    $motelRoomlist[$i]['AvailablePartTimeDeadline'] = getPartTimeDeadline($nowAccomIdx, $dayType);
                }
                else{
                    // 대실 당일 숙박 예약이 있는 경우 => 숙박 입실 시간 -1시간 까지 체크 아웃해야함

                    $motelRoomlist[$i]['AvailablePartTimeDeadline'] = date("H:i:s", strtotime(getTodayAllDayReservation($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckInDate']." -1hours"));
                }

                // 특정 방의 대실 가격을 가져온다. 회원/비회원 + 주중/주말
                
                $motelRoomlist[$i]['PartTimePrice'] = getPartTimePrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);

                // 특정 방의 대실 이용 시간을 가져 온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['PartTimeHour'] = getPartTimeHour($nowAccomIdx, $isMember, $dayType);

            }
            else {

                $motelRoomlist[$i]['IsPartTimeAvailable'] = 'F';

                // 안되는 이유 => 이유1. 그 날 자는(?)연박 손님이 있는 경우 / 2. 대실이 이미 있는 경우

                // 1. 그 날 자는(?)연박 손님이 있는 경우
                if(!checkLongDayReserve($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)){
                    // 딱히 할게 없네
                }
                else{
                    // 2. 이미 대실이 있어서 안되는 경우 => 그 객실의 대실 체크 인, 아웃 타임 출력
                    $motelRoomlist[$i]['ReservedCheckIn'] = getPartTimeCheckInOutTime($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckIn'];
                    $motelRoomlist[$i]['ReservedCheckOut'] = getPartTimeCheckInOutTime($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckOut'];
                }

            }

            // 1-2.당일 숙박이 가능한지 체크한다.
            if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $endAt)){

                // 해당 객실이 숙박이 가능하다면
                // => 1. 당일 대실 예약이 있는 경우, 대실 퇴실 시간 + 1 부터 입실 가능
                // => 2. 당일 대실 예약이 없는 경우, 규정 숙박 입실 시간 부터 가능

                $motelRoomlist[$i]['IsAllDayAvailable'] = 'T';

                // 당일 대실 예약이 있는지 체크한다
                if(checkPartTimeReserve($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)){
                    // 당일 대실이 없는 경우 => 규정 숙박 입실 시간 부터 가능
                    $motelRoomlist[$i]['AvailableAllDayCheckIn'] = getAllDayTime($nowAccomIdx, $isMember, $dayType);
                }
                else{
                    // 당일 대실이 있는 경우 => (대실 퇴실 시간 + 1 시간) 과 규정 숙박 입실 시간 비교해서 늦은(큰) 시간 부터 입실 가능
                    $todayAvailableAllDayCheckInTime = date("H:i:s", strtotime(getPartTimeCheckInOutTime($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckOut']." +1hours"));
                    $rule = getAllDayTime($nowAccomIdx, $isMember, $dayType);
//                    echo $nowAccomIdx.'!!'.$nowRoomIdx.'  ';
//                    echo $todayAvailableAllDayCheckInTime.'zz';
//                    echo $rule;
                    // 비교
                    if($todayAvailableAllDayCheckInTime < $rule)
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $rule;
                    else
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $todayAvailableAllDayCheckInTime;
                }

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);

            }
            else{
                // 해당 객실의 숙박이 가능하지 않다면 => 이유1. 숙박 손님이 있다. / 이유2. 다음날 일찍 대실 예약 손님이 이미 있다.(가정 상황에서의 문제 발생)
                $motelRoomlist[$i]['IsAllDayAvailable'] = 'F';
            }
        }
        // 2. 연박인 경우 => 숙박만 가능
        else {
            // 해당 기간에 연박이 가능한지 본다.
            if(checkConsecutiveStayAvailable($nowAccomIdx, $nowRoomIdx, $startAt, $dayDiff)){
                // 연박이 가능하다면
                // 해당 객실이 숙박이 가능하다면
                // => 1. 당일 대실 예약이 있는 경우, 대실 퇴실 시간 + 1 부터 입실 가능
                // => 2. 당일 대실 예약이 없는 경우, 규정 숙박 입실 시간 부터 가능

                $motelRoomlist[$i]['IsAllDayAvailable'] = 'T';

                // 검사에 활용한 임시 퇴실 시간 변수
                $temp_endAt = date("Y-m-d", strtotime($startAt." +1days"));

                // 당일 대실 예약이 있는지 체크한다
                if(checkPartTimeReserve($nowAccomIdx, $nowRoomIdx, $startAt, $temp_endAt)){
                    // 당일 대실이 없는 경우 => 규정 숙박 입실 시간 부터 가능
                    $motelRoomlist[$i]['AvailableAllDayCheckIn'] = getAllDayTime($nowAccomIdx, $isMember, $dayType);
                }
                else{
                    // 당일 대실이 있는 경우 => (대실 퇴실 시간 + 1 시간) 과 규정 숙박 입실 시간 비교해서 늦은(큰) 시간 부터 입실 가능
                    $todayAvailableAllDayCheckInTime = date("H:i:s", strtotime(getPartTimeCheckInOutTime($nowAccomIdx, $nowRoomIdx, $startAt, $temp_endAt)[0]['CheckOut']." +1hours"));
                    $rule = getAllDayTime($nowAccomIdx, $isMember, $dayType);
//                    echo $nowAccomIdx.'!!'.$nowRoomIdx.'  ';
//                    echo $todayAvailableAllDayCheckInTime.'zz';
//                    echo $rule;
                    // 비교
                    if($todayAvailableAllDayCheckInTime < $rule)
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $rule;
                    else
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $todayAvailableAllDayCheckInTime;
                }

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);


            }
            else{
                // 연박이 안되면
                $motelRoomlist[$i]['IsAllDayAvailable'] = 'F';
            }
        }
    }

    return $motelRoomlist;
}

// [Motel]
// 해당 지역의 조건에 맞는 <<특정>> 모텔의 모든 방 정보 가져오기
function getMotelRoomsInfo($isMember, $startAt, $endAt, $adult, $child, $motelGroupIdx, $AccomIdx){


    // 전날 변수 저장
    $beforeStartAt = date("Y-m-d", strtotime($startAt." -1 day"));
    $beforeEndAt = date("Y-m-d", strtotime($endAt." -1 day"));


    // 평일,주말 판단
    $dayType = getDayType($startAt);

    // 숙박 이용 날짜 차이 구하기
    $dayDiff = (strtotime($endAt) - strtotime($startAt))/60/60/24;

    // 해당 지역 그룹의 특정 숙소의 모든 방 다 가져오기
    $motelRoomlist = getMotelRoomList($motelGroupIdx, $AccomIdx, $adult, $child);



    // 방 마다 돌면서 조건 체크
    for ($i = 0; $i < count($motelRoomlist); $i++) {

        $nowAccomIdx = $motelRoomlist[$i]['AccomIdx'];
        $nowRoomIdx = $motelRoomlist[$i]['RoomIdx'];

        // 1. 1박인 경우 => 숙박 + 대실만 가능
        if ($dayDiff == 1) {

            // 1-1.해당 객실의 대실이 가능하다면
            if (checkPartTimeReserve($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)) {

                $motelRoomlist[$i]['IsPartTimeAvailable'] = 'T';

                // 이전 날 숙박이 있었는지 체크 후 입실 가능 시간 배정
                if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $beforeEndAt)){
                    // 이전 날 숙박이 없었다면 => '10:00:00'
                    $motelRoomlist[$i]['AvailablePartTimeCheckIn'] = '10:00:00';
                }else{
                    // 이전 날 숙박이 있었다면 => (이전 숙박 퇴실 시간 + 1시간) 대실 입실 가능 시간
                    $motelRoomlist[$i]['AvailablePartTimeCheckIn'] =  date("H:i:s", strtotime(getYesterdayAllDayReservation($nowAccomIdx, $nowRoomIdx, $beforeEndAt)[0]['CheckOutDate']." +1hours"));
                }

                // 대실 당일 숙박예약이 있는지 체크 후 퇴실 가능 시간 배정
                if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $endAt)){
                    // 대실 당일 숙박 예약이 없는 경우 = > 대실 퇴실 시간 마감까지 가능
                    $motelRoomlist[$i]['AvailablePartTimeDeadline'] = getPartTimeDeadline($nowAccomIdx, $dayType);
                }
                else{
                    // 대실 당일 숙박 예약이 있는 경우 => 숙박 입실 시간 -1시간 까지 체크 아웃해야함

                    $motelRoomlist[$i]['AvailablePartTimeDeadline'] = date("H:i:s", strtotime(getTodayAllDayReservation($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckInDate']." -1hours"));
                }

                // 특정 방의 대실 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['PartTimePrice'] = getPartTimePrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);

                // 특정 방의 대실 이용 시간을 가져 온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['PartTimeHour'] = getPartTimeHour($nowAccomIdx, $isMember, $dayType);

            }
            else {

                $motelRoomlist[$i]['IsPartTimeAvailable'] = 'F';

                // 안되는 이유 => 이유1. 그 날 자는(?)연박 손님이 있는 경우 / 2. 대실이 이미 있는 경우

                // 1. 그 날 자는(?)연박 손님이 있는 경우
                if(!checkLongDayReserve($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)){
                    // 딱히 할게 없네
                }
                else{
                    // 2. 이미 대실이 있어서 안되는 경우 => 그 객실의 대실 체크 인, 아웃 타임 출력
                    $motelRoomlist[$i]['ReservedCheckIn'] = getPartTimeCheckInOutTime($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckIn'];
                    $motelRoomlist[$i]['ReservedCheckOut'] = getPartTimeCheckInOutTime($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckOut'];
                }

            }

            // 1-2.당일 숙박이 가능한지 체크한다.
            if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $endAt)){

                // 해당 객실이 숙박이 가능하다면
                // => 1. 당일 대실 예약이 있는 경우, 대실 퇴실 시간 + 1 부터 입실 가능
                // => 2. 당일 대실 예약이 없는 경우, 규정 숙박 입실 시간 부터 가능

                $motelRoomlist[$i]['IsAllDayAvailable'] = 'T';

                // 당일 대실 예약이 있는지 체크한다
                if(checkPartTimeReserve($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)){
                    // 당일 대실이 없는 경우 => 규정 숙박 입실 시간 부터 가능
                    $motelRoomlist[$i]['AvailableAllDayCheckIn'] = getAllDayTime($nowAccomIdx, $isMember, $dayType);
                }
                else{
                    // 당일 대실이 있는 경우 => (대실 퇴실 시간 + 1 시간) 과 규정 숙박 입실 시간 비교해서 늦은(큰) 시간 부터 입실 가능
                    $todayAvailableAllDayCheckInTime = date("H:i:s", strtotime(getPartTimeCheckInOutTime($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckOut']." +1hours"));
                    $rule = getAllDayTime($nowAccomIdx, $isMember, $dayType);
//                    echo $nowAccomIdx.'!!'.$nowRoomIdx.'  ';
//                    echo $todayAvailableAllDayCheckInTime.'zz';
//                    echo $rule;
                    // 비교
                    if($todayAvailableAllDayCheckInTime < $rule)
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $rule;
                    else
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $todayAvailableAllDayCheckInTime;
                }

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);

            }
            else{
                // 해당 객실의 숙박이 가능하지 않다면 => 이유1. 숙박 손님이 있다. / 이유2. 다음날 일찍 대실 예약 손님이 이미 있다.(가정 상황에서의 문제 발생)
                $motelRoomlist[$i]['IsAllDayAvailable'] = 'F';
            }
        }
        // 2. 연박인 경우 => 숙박만 가능
        else {
            // 해당 기간에 연박이 가능한지 본다.
            if(checkConsecutiveStayAvailable($nowAccomIdx, $nowRoomIdx, $startAt, $dayDiff)){
                // 연박이 가능하다면
                // 해당 객실이 숙박이 가능하다면
                // => 1. 당일 대실 예약이 있는 경우, 대실 퇴실 시간 + 1 부터 입실 가능
                // => 2. 당일 대실 예약이 없는 경우, 규정 숙박 입실 시간 부터 가능

                $motelRoomlist[$i]['IsAllDayAvailable'] = 'T';

                // 검사에 활용한 임시 퇴실 시간 변수
                $temp_endAt = date("Y-m-d", strtotime($startAt." +1day"));

                // 당일 대실 예약이 있는지 체크한다
                if(checkPartTimeReserve($nowAccomIdx, $nowRoomIdx, $startAt, $temp_endAt)){
                    // 당일 대실이 없는 경우 => 규정 숙박 입실 시간 부터 가능
                    $motelRoomlist[$i]['AvailableAllDayCheckIn'] = getAllDayTime($nowAccomIdx, $isMember, $dayType);
                }
                else{
                    // 당일 대실이 있는 경우 => (대실 퇴실 시간 + 1 시간) 과 규정 숙박 입실 시간 비교해서 늦은(큰) 시간 부터 입실 가능
                    $todayAvailableAllDayCheckInTime = date("H:i:s", strtotime(getPartTimeCheckInOutTime($nowAccomIdx, $nowRoomIdx, $startAt, $temp_endAt)[0]['CheckOut']." +1hours"));
                    $rule = getAllDayTime($nowAccomIdx, $isMember, $dayType);
//                    echo $nowAccomIdx.'!!'.$nowRoomIdx.'  ';
//                    echo $todayAvailableAllDayCheckInTime.'zz';
//                    echo $rule;
                    // 비교
                    if($todayAvailableAllDayCheckInTime < $rule)
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $rule;
                    else
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $todayAvailableAllDayCheckInTime;
                }

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);


            }
            else{
                // 연박이 안되면
                $motelRoomlist[$i]['IsAllDayAvailable'] = 'F';
            }
        }

//        $accomTag = getAccomTag($nowAccomIdx);
//        if(empty($accomTag)){
//            $motelRoomlist[$i]['AccomTag'] = array();
//        }
//        else{
//            $motelRoomlist[$i]['AccomTag'] = $accomTag;
//        }

    }

    return $motelRoomlist;
}

// [Motel]
// 해당 지역의 조건에 맞는 <<특정 모텔>>의 <<특정 객실>> 정보 가져오기
function getMotelRoomDetail($isMember, $startAt, $endAt, $AccomIdx, $RoomIdx){


    // 전날 변수 저장
    $beforeStartAt = date("Y-m-d", strtotime($startAt." -1 day"));
    $beforeEndAt = date("Y-m-d", strtotime($endAt." -1 day"));


    // 평일,주말 판단
    $dayType = getDayType($startAt);

    // 숙박 이용 날짜 차이 구하기
    $dayDiff = (strtotime($endAt) - strtotime($startAt))/60/60/24;

    // 방 마다 돌면서 조건 체크
    for ($i = 0; $i < 1; $i++) {

        // 1. 1박인 경우 => 숙박 + 대실만 가능
        if ($dayDiff == 1) {

            // 1-1.해당 객실의 대실이 가능하다면
            if (checkPartTimeReserve($AccomIdx, $RoomIdx, $startAt, $endAt)) {

                $motelRoomlist[$i]['IsPartTimeAvailable'] = 'T';

                // 이전 날 숙박이 있었는지 체크 후 입실 가능 시간 배정
                if(checkAllDayReserve($AccomIdx, $RoomIdx, $beforeEndAt)){
                    // 이전 날 숙박이 없었다면 => '10:00:00'
                    $motelRoomlist[$i]['AvailablePartTimeCheckIn'] = '10:00:00';
                }else{
                    // 이전 날 숙박이 있었다면 => (이전 숙박 퇴실 시간 + 1시간) 대실 입실 가능 시간
                    $motelRoomlist[$i]['AvailablePartTimeCheckIn'] =  date("H:i:s", strtotime(getYesterdayAllDayReservation($AccomIdx, $RoomIdx, $beforeEndAt)[0]['CheckOutDate']." +1hours"));
                }

                // 대실 당일 숙박예약이 있는지 체크 후 퇴실 가능 시간 배정
                if(checkAllDayReserve($AccomIdx, $RoomIdx, $endAt)){
                    // 대실 당일 숙박 예약이 없는 경우 = > 대실 퇴실 시간 마감까지 가능
                    $motelRoomlist[$i]['AvailablePartTimeDeadline'] = getPartTimeDeadline($AccomIdx, $dayType);
                }
                else{
                    // 대실 당일 숙박 예약이 있는 경우 => 숙박 입실 시간 -1시간 까지 체크 아웃해야함

                    $motelRoomlist[$i]['AvailablePartTimeDeadline'] = date("H:i:s", strtotime(getTodayAllDayReservation($AccomIdx, $RoomIdx, $startAt, $endAt)[0]['CheckInDate']." -1hours"));
                }

                // 특정 방의 대실 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['PartTimePrice'] = getPartTimePrice($AccomIdx, $RoomIdx, $isMember, $dayType);

                // 특정 방의 대실 이용 시간을 가져 온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['PartTimeHour'] = getPartTimeHour($AccomIdx, $isMember, $dayType);

            }
            else {

                $motelRoomlist[$i]['IsPartTimeAvailable'] = 'F';

                // 안되는 이유 => 이유1. 그 날 자는(?)연박 손님이 있는 경우 / 2. 대실이 이미 있는 경우

                // 1. 그 날 자는(?)연박 손님이 있는 경우
                if(!checkLongDayReserve($AccomIdx, $RoomIdx, $startAt, $endAt)){
                    // 딱히 할게 없네
                }
                else{
                    // 2. 이미 대실이 있어서 안되는 경우 => 그 객실의 대실 체크 인, 아웃 타임 출력
                    $motelRoomlist[$i]['ReservedCheckIn'] = getPartTimeCheckInOutTime($AccomIdx, $RoomIdx, $startAt, $endAt)[0]['CheckIn'];
                    $motelRoomlist[$i]['ReservedCheckOut'] = getPartTimeCheckInOutTime($AccomIdx, $RoomIdx, $startAt, $endAt)[0]['CheckOut'];
                }

            }

            // 1-2.당일 숙박이 가능한지 체크한다.
            if(checkAllDayReserve($AccomIdx, $RoomIdx, $endAt)){

                // 해당 객실이 숙박이 가능하다면
                // => 1. 당일 대실 예약이 있는 경우, 대실 퇴실 시간 + 1 부터 입실 가능
                // => 2. 당일 대실 예약이 없는 경우, 규정 숙박 입실 시간 부터 가능

                $motelRoomlist[$i]['IsAllDayAvailable'] = 'T';

                // 당일 대실 예약이 있는지 체크한다
                if(checkPartTimeReserve($AccomIdx, $RoomIdx, $startAt, $endAt)){
                    // 당일 대실이 없는 경우 => 규정 숙박 입실 시간 부터 가능
                    $motelRoomlist[$i]['AvailableAllDayCheckIn'] = getAllDayTime($AccomIdx, $isMember, $dayType);
                }
                else{
                    // 당일 대실이 있는 경우 => (대실 퇴실 시간 + 1 시간) 과 규정 숙박 입실 시간 비교해서 늦은(큰) 시간 부터 입실 가능
                    $todayAvailableAllDayCheckInTime = date("H:i:s", strtotime(getPartTimeCheckInOutTime($AccomIdx, $RoomIdx, $startAt, $endAt)[0]['CheckOut']." +1hours"));
                    $rule = getAllDayTime($AccomIdx, $isMember, $dayType);
//                    echo $AccomIdx.'!!'.$RoomIdx.'  ';
//                    echo $todayAvailableAllDayCheckInTime.'zz';
//                    echo $rule;
                    // 비교
                    if($todayAvailableAllDayCheckInTime < $rule)
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $rule;
                    else
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $todayAvailableAllDayCheckInTime;
                }

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['AllDayPrice'] = getAllDayPrice($AccomIdx, $RoomIdx, $isMember, $dayType);

            }
            else{
                // 해당 객실의 숙박이 가능하지 않다면 => 이유1. 숙박 손님이 있다. / 이유2. 다음날 일찍 대실 예약 손님이 이미 있다.(가정 상황에서의 문제 발생)
                $motelRoomlist[$i]['IsAllDayAvailable'] = 'F';
            }
        }
        // 2. 연박인 경우 => 숙박만 가능
        else {
            // 해당 기간에 연박이 가능한지 본다.
            if(checkConsecutiveStayAvailable($AccomIdx, $RoomIdx, $startAt, $dayDiff)){
                // 연박이 가능하다면
                // 해당 객실이 숙박이 가능하다면
                // => 1. 당일 대실 예약이 있는 경우, 대실 퇴실 시간 + 1 부터 입실 가능
                // => 2. 당일 대실 예약이 없는 경우, 규정 숙박 입실 시간 부터 가능

                $motelRoomlist[$i]['IsAllDayAvailable'] = 'T';

                // 검사에 활용한 임시 퇴실 시간 변수
                $temp_endAt = date("Y-m-d", strtotime($startAt." +1day"));

                // 당일 대실 예약이 있는지 체크한다
                if(checkPartTimeReserve($AccomIdx, $RoomIdx, $startAt, $temp_endAt)){
                    // 당일 대실이 없는 경우 => 규정 숙박 입실 시간 부터 가능
                    $motelRoomlist[$i]['AvailableAllDayCheckIn'] = getAllDayTime($AccomIdx, $isMember, $dayType);
                }
                else{
                    // 당일 대실이 있는 경우 => (대실 퇴실 시간 + 1 시간) 과 규정 숙박 입실 시간 비교해서 늦은(큰) 시간 부터 입실 가능
                    $todayAvailableAllDayCheckInTime = date("H:i:s", strtotime(getPartTimeCheckInOutTime($AccomIdx, $RoomIdx, $startAt, $temp_endAt)[0]['CheckOut']." +1hours"));
                    $rule = getAllDayTime($AccomIdx, $isMember, $dayType);
//                    echo $AccomIdx.'!!'.$RoomIdx.'  ';
//                    echo $todayAvailableAllDayCheckInTime.'zz';
//                    echo $rule;
                    // 비교
                    if($todayAvailableAllDayCheckInTime < $rule)
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $rule;
                    else
                        $motelRoomlist[$i]['AvailableAllDayCheckIn'] = $todayAvailableAllDayCheckInTime;
                }

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelRoomlist[$i]['AllDayPrice'] = getAllDayPrice($AccomIdx, $RoomIdx, $isMember, $dayType);


            }
            else{
                // 연박이 안되면
                $motelRoomlist[$i]['IsAllDayAvailable'] = 'F';
            }
        }

//        $accomTag = getAccomTag($AccomIdx);
//        if(empty($accomTag)){
//            $motelRoomlist[$i]['AccomTag'] = array();
//        }
//        else{
//            $motelRoomlist[$i]['AccomTag'] = $accomTag;
//        }

    }

    return $motelRoomlist[0];
}

// [호텔]
// 해당 지역의 조건에 맞는 모든 호텔 방들의 정보를 그룹핑한다.
function getHotels($isMember, $startAt, $endAt, $hotelGroupIdx, $adult, $child)
{
    // 0.  결과배열 선언 및 초기화
    $hotels = array();

    // 1.인원 조건에 맞는 모텔의 객실에 대한 정보를 가져온다.
    $hotelRoomInfo = getAllHotelRoomsInfo($isMember, $startAt, $endAt, $hotelGroupIdx, $adult, $child);

    // 2. 조건을 만족하는 객실의  총 개수 => 구성이 어떤지는 모름
    $numOfTotalRoom = count($hotelRoomInfo);

    // 조건에 맞는 방이 하나도 없는 경우 => 빈 문자열 리턴
    if ($numOfTotalRoom == 0) return '';

    // 3. 조건을 만족하는 숙소의 AccomIdx 리스트, 개수
    $AccomList = getHotelAccomList($hotelGroupIdx, $adult, $child); // 6, 7
    $numOfAccom = count($AccomList); // 2

    // 4. 조건에 맞는 객실이 숙소마다 몇 개인지 파악한다. => 문자열에서 연속된 같은 숫자 세기
    // AccomList와 대응하는 방들의 개수가 채워진다. => AccomList의 n번째 Accomidx는 roomCount의 n번째 값만큼 방 개수를 가진다.
    $numOfRoomByAccom = array();
    $count = 1;

    // 1개면 루프를 못 돈다.
    if ($numOfAccom == 1) {
        $numOfRoomByAccom[0] = 1;
    } else {

        for ($i = 0; $i < $numOfTotalRoom - 1; $i++) { // 1번 돌거임
            if ($hotelRoomInfo[$i]['AccomIdx'] == $hotelRoomInfo[$i + 1]['AccomIdx']) {
                // 뒤에 AccomIdx와 같은 경우
                $count++;
            } else {
                // 뒤에 AccomIdx문자와 다른 경우
                $numOfRoomByAccom[] = $count;
                $count = 1;
            }
            // 마지막엔 강제로 넣어준다.
            if ($i == $numOfTotalRoom - 2)
                $numOfRoomByAccom[] = $count;
        }
    }

    // 5. 각 숙소마다 그룹핑한다.
    $roomCount = 0;

    // 숙소 개수만큼 돈다.
    for ($i = 0; $i < $numOfAccom; $i++) {

        // 숙소당 처음 판단하는 값인지 판단.
        $isFirstForAllDay = true;

        // AccomIdx 추가
        $hotels[$i]['AccomIdx'] = $hotelRoomInfo[$roomCount]['AccomIdx'];
        $hotels[$i]['AccomName'] = getAccomInfo($hotels[$i]['AccomIdx'])['AccomName'];
        $hotels[$i]['AccomThumbnailUrl'] = getAccomInfo($hotels[$i]['AccomIdx'])['AccomThumbnailUrl'];
        $hotels[$i]['AccomGrade'] = getHotelGrade($hotels[$i]['AccomIdx'])['AccomGrade'];
        $hotels[$i]['Authentication'] = getHotelGrade($hotels[$i]['AccomIdx'])['Authentication'];
        $hotels[$i]['AvgRating'] = getAccomInfo($hotels[$i]['AccomIdx'])['avgRating'];
        $hotels[$i]['NumOfReview'] = getAccomInfo($hotels[$i]['AccomIdx'])['numOfReview'];
        $hotels[$i]['NumOfUserPick'] = getUserPick($hotels[$i]['AccomIdx']);
        $hotels[$i]['GuideFromStation'] = getAccomInfo($hotels[$i]['AccomIdx'])['GuideFromStation'];

        // 숙소당 방 개수맘큼 돈다.
        for ($j = 0; $j < $numOfRoomByAccom[$i]; $j++) {

            /* * * * * * * * * * * * *
             *  2. 숙박 가능여부 체크   *
             * * * * * * * * * * * * */

            // 2-1. 숙박이 가능한 경우
            if ($hotelRoomInfo[$roomCount]['IsAllDayAvailable'] == 'T') {

                // 첫 번째 숙박 가능한 숙소의 경우 가격 비교 과정 X
                if ($isFirstForAllDay) {
                    // 첫 번째는 그냥 할당.
                    $isFirstForAllDay = false;
                    $hotels[$i]['IsAllDayAvailable'] = $hotelRoomInfo[$roomCount]['IsAllDayAvailable'];
                    $hotels[$i]['AvailableAllDayCheckIn'] = $hotelRoomInfo[$roomCount]['AvailableAllDayCheckIn'];
                    $hotels[$i]['AllDayPrice'] = $hotelRoomInfo[$roomCount]['AllDayPrice'];
                }
                // 두 번째 숙박 가능한 숙소부터는 가격 비교 시작
                else {
                    // 새로운 숙박비 < 기존의 숙박비 ===> 결과 배열에 할당
                    if ($hotelRoomInfo[$roomCount]['AllDayPrice'] < $hotels[$i]['AllDayPrice']) {
                        $hotels[$i]['AvailableAllDayCheckIn'] = $hotelRoomInfo[$roomCount]['AvailableAllDayCheckIn'];
                        $hotels[$i]['AllDayPrice'] = $hotelRoomInfo[$roomCount]['AllDayPrice'];
                    }
                }
            }

            // 다음 방 체크
            $roomCount++;
        }

        // 모두 숙박 불가능 경우 ==> 판단이 한 번도 일어나지 않은 경우
        if ($isFirstForAllDay) {
            $hotels[$i]['IsAllDayAvailable'] = 'F';
        }

        $accomTag = getAccomTag($hotels[$i]['AccomIdx']);
        if(empty($accomTag)){
            $hotels[$i]['AccomTag'] = array();
        }
        else{
            $hotels[$i]['AccomTag'] = $accomTag;
        }



    }

    return $hotels;
}

// [호텔]
// 해당 지역의 조건에 맞는 모든 호텔들 모든 방 정보 가져오기
function getAllHotelRoomsInfo($isMember, $startAt, $endAt, $hotelGroupIdx, $adult, $child)
{
    // 평일,주말 판단
    $dayType = getDayType($startAt);

    // 숙박 이용 날짜 차이 구하기
    $dayDiff = (strtotime($endAt) - strtotime($startAt))/60/60/24;

    // 해당 지역 그룹의 모든 방 다 가져오기
    $hotelRoomlist = getAllHotelRoomList($hotelGroupIdx, $adult, $child);

    // 방 마다 돌면서 조건 체크
    for ($i = 0; $i < count($hotelRoomlist); $i++) {

        $nowAccomIdx = $hotelRoomlist[$i]['AccomIdx'];
        $nowRoomIdx = $hotelRoomlist[$i]['RoomIdx'];

        // 1. 1박인 경우
        if ($dayDiff == 1) {

            // 1-1.당일 숙박이 가능한지 체크한다.
            if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $endAt)){

                // 해당 객실이 숙박이 가능하다면
                $hotelRoomlist[$i]['IsAllDayAvailable'] = 'T';

                // 특정 방의 입실 가능 시간을 가져온다.회원/비회원 + 주중/주말
                $hotelRoomlist[$i]['AvailableAllDayCheckIn']  = getAllDayTime($nowAccomIdx, $isMember, $dayType);

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $hotelRoomlist[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);
            }
            else{
                // 해당 객실의 숙박이 가능하지 않다면 => 이유1. 숙박 손님이 있다.
                $hotelRoomlist[$i]['IsAllDayAvailable'] = 'F';
            }
        }
        // 2. 연박인 경우
        else {
            // 해당 기간에 연박이 가능한지 본다.
            if(hotelLongStayChecker($nowAccomIdx, $nowRoomIdx, $startAt, $dayDiff)){
                // 연박이 가능하다면
                $hotelRoomlist[$i]['IsAllDayAvailable'] = 'T';

                // 특정 방의 입실 가능 시간을 가져온다.회원/비회원 + 주중/주말
                $hotelRoomlist[$i]['AvailableAllDayCheckIn']  = getAllDayTime($nowAccomIdx, $isMember, $dayType);

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $hotelRoomlist[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);
            }
            else{
                // 연박이 안되면
                $hotelRoomlist[$i]['IsAllDayAvailable'] = 'F';
            }
        }
    }

    return $hotelRoomlist;
}

// [호텔]
//  해당 지역의 조건에 맞는 <<특정>> 호텔의 모든 방 정보 가져오기
function getHotelRoomsInfo($isMember, $startAt, $endAt, $adult, $child, $hotelGroupIdx, $AccomIdx)
{
    // 평일,주말 판단
    $dayType = getDayType($startAt);

    // 숙박 이용 날짜 차이 구하기
    $dayDiff = (strtotime($endAt) - strtotime($startAt))/60/60/24;

    // 해당 지역 그룹의 모든 방 다 가져오기
    $hotelRoomlist = getHotelRoomList($hotelGroupIdx, $AccomIdx, $adult, $child);

    // 방 마다 돌면서 조건 체크
    for ($i = 0; $i < count($hotelRoomlist); $i++) {

        $nowAccomIdx = $hotelRoomlist[$i]['AccomIdx'];
        $nowRoomIdx = $hotelRoomlist[$i]['RoomIdx'];

        // 1. 1박인 경우
        if ($dayDiff == 1) {

            // 1-1.당일 숙박이 가능한지 체크한다.
            if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $endAt)){

                // 해당 객실이 숙박이 가능하다면
                $hotelRoomlist[$i]['IsAllDayAvailable'] = 'T';

                // 특정 방의 입실 가능 시간을 가져온다.회원/비회원 + 주중/주말
                $hotelRoomlist[$i]['AvailableAllDayCheckIn']  = getAllDayTime($nowAccomIdx, $isMember, $dayType);

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $hotelRoomlist[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);
            }
            else{
                // 해당 객실의 숙박이 가능하지 않다면 => 이유1. 숙박 손님이 있다.
                $hotelRoomlist[$i]['IsAllDayAvailable'] = 'F';
            }
        }
        // 2. 연박인 경우
        else {
            // 해당 기간에 연박이 가능한지 본다.
            if(hotelLongStayChecker($nowAccomIdx, $nowRoomIdx, $startAt, $dayDiff)){
                // 연박이 가능하다면
                $hotelRoomlist[$i]['IsAllDayAvailable'] = 'T';

                // 특정 방의 입실 가능 시간을 가져온다.회원/비회원 + 주중/주말
                $hotelRoomlist[$i]['AvailableAllDayCheckIn']  = getAllDayTime($nowAccomIdx, $isMember, $dayType);

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $hotelRoomlist[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);
            }
            else{
                // 연박이 안되면
                $hotelRoomlist[$i]['IsAllDayAvailable'] = 'F';
            }
        }
    }

    return $hotelRoomlist;
}

// [호텔]
// 해당 지역의 조건에 맞는 <<특정 호텔>>의 <<특정 객실>> 정보 가져오기
function getHotelRoomDetail($isMember, $startAt, $endAt, $AccomIdx, $RoomIdx){

    // 평일,주말 판단
    $dayType = getDayType($startAt);

    // 숙박 이용 날짜 차이 구하기
    $dayDiff = (strtotime($endAt) - strtotime($startAt))/60/60/24;



    // 1. 1박인 경우
    if ($dayDiff == 1) {

        // 1-1.당일 숙박이 가능한지 체크한다.
        if (checkAllDayReserve($AccomIdx, $RoomIdx, $endAt)) {

            // 해당 객실이 숙박이 가능하다면
            $hotelRoom['IsAllDayAvailable'] = 'T';

            // 특정 방의 입실 가능 시간을 가져온다.회원/비회원 + 주중/주말
            $hotelRoom['AvailableAllDayCheckIn'] = getAllDayTime($AccomIdx, $isMember, $dayType);

            // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
            $hotelRoom['AllDayPrice'] = getAllDayPrice($AccomIdx, $RoomIdx, $isMember, $dayType);
        } else {
            // 해당 객실의 숙박이 가능하지 않다면 => 이유1. 숙박 손님이 있다.
            $hotelRoom['IsAllDayAvailable'] = 'F';
        }
    } // 2. 연박인 경우
    else {
        // 해당 기간에 연박이 가능한지 본다.
        if (hotelLongStayChecker($AccomIdx, $RoomIdx, $startAt, $dayDiff)) {
            // 연박이 가능하다면
            $hotelRoom['IsAllDayAvailable'] = 'T';

            // 특정 방의 입실 가능 시간을 가져온다.회원/비회원 + 주중/주말
            $hotelRoom['AvailableAllDayCheckIn'] = getAllDayTime($AccomIdx, $isMember, $dayType);

            // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
            $hotelRoom['AllDayPrice'] = getAllDayPrice($AccomIdx, $RoomIdx, $isMember, $dayType);
        } else {
            // 연박이 안되면
            $hotelRoom['IsAllDayAvailable'] = 'F';
        }
    }


    return $hotelRoom;
}

// [종합]해당 지역의 조건에 맞는 <<특정 숙소>>의 <<특정 객실>> 정보 가져오기
function getRoomDetail($isMember, $startAt, $endAt, $AccomIdx, $RoomIdx){

    // 모텔인 경우
    if(getTypeOfAccom($AccomIdx) == 'M')
        $res = getMotelRoomDetail($isMember, $startAt, $endAt, $AccomIdx, $RoomIdx);
    // 호텔인 경우
    else
        $res = getHotelRoomDetail($isMember, $startAt, $endAt, $AccomIdx, $RoomIdx);


    return $res;
}
