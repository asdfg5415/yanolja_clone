<?php


// 하단 네비게이션 바 지역별 버튼 => 지역리스트 출력
function getAreas(){
    $pdo = pdoSqlConnect();
    $query = "
                select distinct cityIdx, cityName, MotelGroup.MotelGroupIdx as GroupIdx , MotelGroupName as GroupName
                from Region
                 join MotelGroup on MotelGroup.RegionIdx = Region.RegionIdx
                 join MotelGroupName on MotelGroupName.MotelGroupIdx = MotelGroup.MotelGroupIdx
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 하단 네비게이션 바 지역별 버튼 => 지역리스트 출력 => 해당 지역 숙소(종류 상관없이) 출력
function getAccomByArea($groupIdx, $isMember, $startAt, $endAt, $adult, $child){

    // 1. 해당 지역의 모텔과 호텔의 모든 룸을 가져온다.
    $motelListByArea = getRoomListByArea($groupIdx, $adult, $child, 'M');
    $hotelListByArea = getRoomListByArea($groupIdx, $adult, $child, 'H');

    $motels = array();
    $hotels = array();

    if(count($motelListByArea))
        $motels = getMotelsByArea($groupIdx, $isMember, $startAt, $endAt, $adult, $child, $motelListByArea);
    if(count($hotelListByArea))
        $hotels = getHotelsByArea($groupIdx, $isMember, $startAt, $endAt, $adult, $child, $hotelListByArea);

    $res['motel'] = $motels;
    $res['hotel'] = $hotels;

    return $res;
}

// 통합 지역에서의 모텔 객실 정보들을 그룹핑한다.
function getMotelsByArea($groupIdx, $isMember, $startAt, $endAt, $adult, $child, $motelListByArea)
{
    // 0.  결과배열 선언 및 초기화
    $motels = array();

    // 1.인원 조건에 맞는 모텔의 객실에 대한 정보를 가져온다.
    $motelRoomInfo = getMotelRoomsInfoByArea($isMember, $startAt, $endAt, $motelListByArea);

    // 2. 조건을 만족하는 객실의  총 개수 => 구성이 어떤지는 모름
    $numOfTotalRoom = count($motelRoomInfo);

    // 조건에 맞는 방이 하나도 없는 경우 => 빈 문자열 리턴
    if ($numOfTotalRoom == 0) return '';

    // 3. 조건을 만족하는 숙소의 AccomIdx 리스트, 개수
    $AccomList = getAccomListByArea($groupIdx, $adult, $child, 'M');
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
        $motels[$i]['AccomType'] = getAccomInfo($motels[$i]['AccomIdx'])['AccomType'];
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

// 통합 지역에서의 호텔 객실 정보들을 그룹핑한다.
function getHotelsByArea($groupIdx, $isMember, $startAt, $endAt, $adult, $child, $hotelListByArea)
{
    // 0.  결과배열 선언 및 초기화
    $hotels = array();

    // 1.인원 조건에 맞는 모텔의 객실에 대한 정보를 가져온다.
    $hotelRoomInfo = getHotelRoomsInfoByArea($isMember, $startAt, $endAt, $hotelListByArea);

    // 2. 조건을 만족하는 객실의  총 개수 => 구성이 어떤지는 모름
    $numOfTotalRoom = count($hotelRoomInfo);

    // 조건에 맞는 방이 하나도 없는 경우 => 빈 문자열 리턴
    if ($numOfTotalRoom == 0) return '';

    // 3. 조건을 만족하는 숙소의 AccomIdx 리스트, 개수
    $AccomList = getAccomListByArea($groupIdx, $adult, $child, 'H'); // 6, 7
    $numOfAccom = count($AccomList); // 2


    // 4. 조건에 맞는 객실이 숙소마다 몇 개인지 파악한다. => 문자열에서 연속된 같은 숫자 세기
    // AccomList와 대응하는 방들의 개수가 채워진다. => AccomList의 n번째 Accomidx는 roomCount의 n번째 값만큼 방 개수를 가진다.
    $numOfRoomByAccom = array();
    $count = 1;

    // 1개면 루프를 못 돈다. => 할당이 안됨
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
        $hotels[$i]['AccomType'] = getAccomInfo($hotels[$i]['AccomIdx'])['AccomType'];
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

// 통합 지역에서 받아온 모텔들의 객실 정보를 가져온다.
function getMotelRoomsInfoByArea($isMember, $startAt, $endAt, $motelListByArea)
{

    // 전날 변수 저장
    $beforeStartAt = date("Y-m-d", strtotime($startAt." -1 day"));
    $beforeEndAt = date("Y-m-d", strtotime($endAt." -1 day"));


    // 평일,주말 판단
    $dayType = getDayType($startAt);

    // 숙박 이용 날짜 차이 구하기
    $dayDiff = (strtotime($endAt) - strtotime($startAt))/60/60/24;

    // 방 마다 돌면서 조건 체크
    for ($i = 0; $i < count($motelListByArea); $i++) {

        $nowAccomIdx = $motelListByArea[$i]['AccomIdx'];
        $nowRoomIdx = $motelListByArea[$i]['RoomIdx'];

        // 1. 1박인 경우 => 숙박 + 대실만 가능
        if ($dayDiff == 1) {

            // 1-1.해당 객실의 대실이 가능하다면
            if (checkPartTimeReserve($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)) {

                $motelListByArea[$i]['IsPartTimeAvailable'] = 'T';

                // 이전 날 숙박이 있었는지 체크 후 입실 가능 시간 배정
                if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $beforeEndAt)){
                    // 이전 날 숙박이 없었다면 => '10:00:00'
                    $motelListByArea[$i]['AvailablePartTimeCheckIn'] = '10:00:00';
                }else{
                    // 이전 날 숙박이 있었다면 => (이전 숙박 퇴실 시간 + 1시간) 대실 입실 가능 시간
                    $motelListByArea[$i]['AvailablePartTimeCheckIn'] =  date("H:i:s", strtotime(getYesterdayAllDayReservation($nowAccomIdx, $nowRoomIdx, $beforeEndAt)[0]['CheckOutDate']." +1hours"));
                }

                // 대실 당일 숙박예약이 있는지 체크 후 퇴실 가능 시간 배정
                if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $endAt)){
                    // 대실 당일 숙박 예약이 없는 경우 = > 대실 퇴실 시간 마감까지 가능
                    $motelListByArea[$i]['AvailablePartTimeDeadline'] = getPartTimeDeadline($nowAccomIdx, $dayType);
                }
                else{
                    // 대실 당일 숙박 예약이 있는 경우 => 숙박 입실 시간 -1시간 까지 체크 아웃해야함

                    $motelListByArea[$i]['AvailablePartTimeDeadline'] = date("H:i:s", strtotime(getTodayAllDayReservation($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckInDate']." -1hours"));
                }

                // 특정 방의 대실 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelListByArea[$i]['PartTimePrice'] = getPartTimePrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);

                // 특정 방의 대실 이용 시간을 가져 온다. 회원/비회원 + 주중/주말
                $motelListByArea[$i]['PartTimeHour'] = getPartTimeHour($nowAccomIdx, $isMember, $dayType);

            }
            else {

                $motelListByArea[$i]['IsPartTimeAvailable'] = 'F';

                // 안되는 이유 => 이유1. 그 날 자는(?)연박 손님이 있는 경우 / 2. 대실이 이미 있는 경우

                // 1. 그 날 자는(?)연박 손님이 있는 경우
                if(!checkLongDayReserve($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)){
                    // 딱히 할게 없네
                }
                else{
                    // 2. 이미 대실이 있어서 안되는 경우 => 그 객실의 대실 체크 인, 아웃 타임 출력
                    $motelListByArea[$i]['ReservedCheckIn'] = getPartTimeCheckInOutTime($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckIn'];
                    $motelListByArea[$i]['ReservedCheckOut'] = getPartTimeCheckInOutTime($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)[0]['CheckOut'];
                }

            }

            // 1-2.당일 숙박이 가능한지 체크한다.
            if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $endAt)){

                // 해당 객실이 숙박이 가능하다면
                // => 1. 당일 대실 예약이 있는 경우, 대실 퇴실 시간 + 1 부터 입실 가능
                // => 2. 당일 대실 예약이 없는 경우, 규정 숙박 입실 시간 부터 가능

                $motelListByArea[$i]['IsAllDayAvailable'] = 'T';

                // 당일 대실 예약이 있는지 체크한다
                if(checkPartTimeReserve($nowAccomIdx, $nowRoomIdx, $startAt, $endAt)){
                    // 당일 대실이 없는 경우 => 규정 숙박 입실 시간 부터 가능
                    $motelListByArea[$i]['AvailableAllDayCheckIn'] = getAllDayTime($nowAccomIdx, $isMember, $dayType);
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
                        $motelListByArea[$i]['AvailableAllDayCheckIn'] = $rule;
                    else
                        $motelListByArea[$i]['AvailableAllDayCheckIn'] = $todayAvailableAllDayCheckInTime;
                }

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelListByArea[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);

            }
            else{
                // 해당 객실의 숙박이 가능하지 않다면 => 이유1. 숙박 손님이 있다. / 이유2. 다음날 일찍 대실 예약 손님이 이미 있다.(가정 상황에서의 문제 발생)
                $motelListByArea[$i]['IsAllDayAvailable'] = 'F';
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

                $motelListByArea[$i]['IsAllDayAvailable'] = 'T';

                // 검사에 활용한 임시 퇴실 시간 변수
                $temp_endAt = date("Y-m-d", strtotime($startAt." +1day"));

                // 당일 대실 예약이 있는지 체크한다
                if(checkPartTimeReserve($nowAccomIdx, $nowRoomIdx, $startAt, $temp_endAt)){
                    // 당일 대실이 없는 경우 => 규정 숙박 입실 시간 부터 가능
                    $motelListByArea[$i]['AvailableAllDayCheckIn'] = getAllDayTime($nowAccomIdx, $isMember, $dayType);
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
                        $motelListByArea[$i]['AvailableAllDayCheckIn'] = $rule;
                    else
                        $motelListByArea[$i]['AvailableAllDayCheckIn'] = $todayAvailableAllDayCheckInTime;
                }

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $motelListByArea[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);


            }
            else{
                // 연박이 안되면
                $motelListByArea[$i]['IsAllDayAvailable'] = 'F';
            }
        }
    }

    return $motelListByArea;
}

// 통합 지역에서 받아온 호텔들의 객실 정보를 가져온다.
function getHotelRoomsInfoByArea($isMember, $startAt, $endAt, $hotelListByArea)
{
    // 평일,주말 판단
    $dayType = getDayType($startAt);

    // 숙박 이용 날짜 차이 구하기
    $dayDiff = (strtotime($endAt) - strtotime($startAt))/60/60/24;

    // 방 마다 돌면서 조건 체크
    for ($i = 0; $i < count($hotelListByArea); $i++) {

        $nowAccomIdx = $hotelListByArea[$i]['AccomIdx'];
        $nowRoomIdx = $hotelListByArea[$i]['RoomIdx'];

        // 1. 1박인 경우
        if ($dayDiff == 1) {

            // 1-1.당일 숙박이 가능한지 체크한다.
            if(checkAllDayReserve($nowAccomIdx, $nowRoomIdx, $endAt)){

                // 해당 객실이 숙박이 가능하다면
                $hotelListByArea[$i]['IsAllDayAvailable'] = 'T';

                // 특정 방의 입실 가능 시간을 가져온다.회원/비회원 + 주중/주말
                $hotelListByArea[$i]['AvailableAllDayCheckIn']  = getAllDayTime($nowAccomIdx, $isMember, $dayType);

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $hotelListByArea[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);
            }
            else{
                // 해당 객실의 숙박이 가능하지 않다면 => 이유1. 숙박 손님이 있다.
                $hotelListByArea[$i]['IsAllDayAvailable'] = 'F';
            }
        }
        // 2. 연박인 경우
        else {
            // 해당 기간에 연박이 가능한지 본다.
            if(hotelLongStayChecker($nowAccomIdx, $nowRoomIdx, $startAt, $dayDiff)){
                // 연박이 가능하다면
                $hotelListByArea[$i]['IsAllDayAvailable'] = 'T';

                // 특정 방의 입실 가능 시간을 가져온다.회원/비회원 + 주중/주말
                $hotelListByArea[$i]['AvailableAllDayCheckIn']  = getAllDayTime($nowAccomIdx, $isMember, $dayType);

                // 특정 방의 숙박 가격을 가져온다. 회원/비회원 + 주중/주말
                $hotelListByArea[$i]['AllDayPrice'] = getAllDayPrice($nowAccomIdx, $nowRoomIdx, $isMember, $dayType);
            }
            else{
                // 연박이 안되면
                $hotelListByArea[$i]['IsAllDayAvailable'] = 'F';
            }
        }
    }

    return $hotelListByArea;
}

// 통합 지역에서 인원 구성 조건에 맞는 AccomIdx를 가져온다.
function getAccomListByArea($groupIdx, $adult, $child, $AccomType){
    $pdo = pdoSqlConnect();

    // motel 이 지역이 제일 세분화되서 나눠져있기 때문에 통합지역 그룹용으로도 쓴다.
    // 사실 제일 작게 나눈 구역을 모텔이 쓰고 있다고 보는게 맞다.

    $query = "
                select distinct Room.AccomIdx
                from Accommodation
                         join Room on Accommodation.AccomIdx = Room.AccomIdx
                where (RegionIdx) in
                      (select Region.RegionIdx
                       from Region
                                join MotelGroup on Region.RegionIdx = MotelGroup.RegionIdx
                       where MotelGroupIdx = ?)
                  and ? + ? <= MaxCapacity and AccomType = ?

    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$groupIdx, $adult, $child, $AccomType]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 통합 지역에서 인원 구성 조건에 맞는 AccomIdx, RoomIdx를 가져온다.
function getRoomListByArea($groupIdx, $adult, $child, $AccomType){
    $pdo = pdoSqlConnect();

    // motel 이 지역이 제일 세분화되서 나눠져있기 때문에 통합지역 그룹용으로도 쓴다.
    // 사실 제일 작게 나눈 구역을 모텔이 쓰고 있다고 보는게 맞다.

    $query = "
                select Room.AccomIdx, RoomIdx
                from Accommodation
                         join Room on Accommodation.AccomIdx = Room.AccomIdx
                where (RegionIdx) in
                      (select Region.RegionIdx
                       from Region
                                join MotelGroup on Region.RegionIdx = MotelGroup.RegionIdx
                       where MotelGroupIdx = ?)
                  and ? + ? <= MaxCapacity and AccomType = ?

    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$groupIdx, $adult, $child, $AccomType]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}
