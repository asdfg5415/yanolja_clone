<?php


// 특정 idx를 가진 숙소의 이름, 평균평점, 리뷰 수 가져오는 함수
function getAccomInfo($accomIdx)
{
    $pdo = pdoSqlConnect();
    $query = "
                select AccomName,
                       AccomType,
                       AccomThumbnailUrl,
                       If(isnull(t1.avgRating), 0, avgRating) as avgRating,
                       If(isnull(count(*)), 0, count(*)) as numOfReview,
                        GuideFromStation,
                       Accommodation.AccomLatitude,
                       Accommodation.AccomLongitude,
                       Accommodation.AccomAddress,
                       Accommodation.AccomIntroduction,
                       Accommodation.AccomGuide
                from Accommodation
                         join (select AccommodationReview.AccomIdx, avg(OverallRating) as avgRating
                               from Accommodation
                                        join AccommodationReview on Accommodation.AccomIdx = AccommodationReview.AccomIdx
                               group by Accommodation.AccomIdx) as t1
                              on Accommodation.AccomIdx = t1.AccomIdx
                         join AccommodationReview
                              on AccommodationReview.AccomIdx = Accommodation.AccomIdx
                where Accommodation.AccomIdx = ?;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$accomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}

// 특정 숙소의 Tag 개수
function getAccomTag($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "
                select AccommodationTag.TagIdx, TagName
                from AccommodationTag join TagList on AccommodationTag.TagIdx = TagList.TagIdx
                where AccomIdx = ?
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

// 특정 숙소의 유저 찜 개수
function getUserPick($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "
                select count(*) as cnt
                from UserPick
                where AccomIdx = ?;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['cnt'];
}

// 특정 숙소의 사진들 가져오기
function getAccomPhotos($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "
                select PhotoType,
                       IF(isnull(AccomodationPhotos.RoomIdx), 0, AccomodationPhotos.RoomIdx) as RoomIdx,
                       IF(isnull(RoomName), 0, RoomName) as RoomName,
                       AccomodationPhotos.PhotoInfo,
                       PhotoUrl
                from AccomodationPhotos
                         left join Room on AccomodationPhotos.AccomIdx = Room.AccomIdx and AccomodationPhotos.RoomIdx = Room.RoomIdx
                where AccomodationPhotos.AccomIdx = ?;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

//    $result =array();
//
//    for($i = 0; $i < count($res); $i++){
//        // 포토 타입
//        if(strcmp($res[$i]['PhotoType'], 'M') == 0){
//            $result['Main'][] = $res[$i]['PhotoUrl'];
//        }
//        // 룸idx
//        if($res[$i]['RoomIdx'] != 0){
//            $result['Room'][$res[$i]['RoomName']][] = $res[$i]['PhotoUrl'];
//        }
//        // 숙소분위기
//        else{
//            $result['Mood'][$res[$i]['PhotoInfo']][] = $res[$i]['PhotoUrl'];
//        }
//    }

    return $res;
}

// 특정 숙소의 리뷰와 리뷰 답글 가져오기
function getAccomReviewWithReply($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "SELECT
    BR.UserIdx,
    BR.ReviewIdx,
    UR.UserName,
    RoomName,
    UR.ReserveType,
    BR.ReviewContent,
    BR.OverallRating,
	CASE
		WHEN
			(timestampdiff(second, BR.CreatedAt, now()) < 60)
		THEN
			concat(timestampdiff(second, BR.CreatedAt, now()), '초 전')
		ELSE
			CASE
				WHEN
					(timestampdiff(minute, BR.CreatedAt, now()) < 60)
				THEN
					concat(timestampdiff(minute, BR.CreatedAt, now()), '분 전')
				ELSE
					CASE
						WHEN
							(timestampdiff(hour, BR.CreatedAt, now()) < 24)
						THEN
							concat(timestampdiff(hour, BR.CreatedAt, now()), '시간 전')
						ELSE
							CASE
								WHEN
									(timestampdiff(day, BR.CreatedAt, now()) < 8)
								THEN
									concat(timestampdiff(day, BR.CreatedAt, now()), '일 전')
								ELSE
									BR.CreatedAt
							END
					END
			END
	END as WrittenTime,
    CASE
        WHEN
            BR.IsPhotoReview = 'Y'
        THEN
            (SELECT
                    GROUP_CONCAT(PhotoUrl)
                FROM
                    ReviewPhoto
                WHERE
                    ReviewPhoto.ReviewIdx = BR.ReviewIdx
                GROUP BY ReviewIdx)
    END AS ReviewPhoto,
    CASE
		WHEN
			(SELECT EXISTS (Select ReviewIdx FROM ReviewReply WHERE ReviewReply.ReviewIdx = BR.ReviewIdx)) = 1
		THEN
			(SELECT ReplyText
				FROM ReviewReply
			WHERE ReviewReply.ReviewIdx = BR.ReviewIdx
            AND ReviewReply.IsDeleted = 'N')
	END as ReviewReply,
    CASE
		WHEN
			(SELECT EXISTS (Select ReviewIdx FROM ReviewReply WHERE ReviewReply.ReviewIdx = BR.ReviewIdx)) = 1
		THEN
			(SELECT CreatedAt
				FROM ReviewReply
			WHERE ReviewReply.ReviewIdx = BR.ReviewIdx
            AND ReviewReply.IsDeleted = 'N')
	END as ReplyWrittenTime
FROM
    (select AccommodationReview.AccomIdx, AccommodationReview.ReviewIdx,AccommodationReview.UserIdx,AccommodationReview.ReviewContent
 ,AccommodationReview.IsPhotoReview, AccommodationReview.OverallRating, AccommodationReview.KindnessRating, AccommodationReview.CleanlinessRating,
 AccommodationReview.ConvenienceRating, AccommodationReview.LocationRating, AccommodationReview.CreatedAt, AccommodationReview.UpdatedAt, AccommodationReview.isDeleted
 from (AccommodationReview join BestReview
ON (AccommodationReview.AccomIdx = BestReview.AccomIdx and AccommodationReview.ReviewIdx = BestReview.ReviewIdx))) BR
    JOIN
    (SELECT
        UserIdx, UserName, Reservation.AccomIdx, RoomName, ReserveType, ReserveIdx
    FROM
        User
			JOIN (Reservation JOIN Room On (Reservation.AccomIdx = Room.AccomIdx and Reservation.RoomIdx = Room.RoomIdx))
		USING (UserIdx)) UR ON (UR.UserIdx = BR.UserIdx
        AND UR.AccomIdx = BR.AccomIdx)
WHERE
    BR.AccomIdx = ?     AND BR.IsDeleted = 'N'
ORDER BY CreatedAt DESC
LIMIT 2;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

// 리뷰에 대한 답변 가져오기
function getNumOfReviewReply($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "
                select count(*) as cnt
                from ReviewReply
                where AccomIdx = ?;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['cnt'];
}

// 숙소의 전화번호  가져오기
function getAccomContact($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "
                select AccomContact as Contact
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

    return $res[0]['Contact'];
}

//  숙소의 주차 가능 여부 가져오기 => 오늘 날짜에만 쓰임
function getAccomParkingStatus($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "
                select IsFullParking
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

    return $res[0]['IsFullParking'];
}

// 숙소의 편의 시설 가져옴
function getAccomFacilities($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "
                select FacilityName
                from AccommodationFacilities
                where AccomIdx = ?;
    ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;
    $result = array();
    for ($i = 0; $i < count($res); $i++) {
        $result[] = $res[$i]['FacilityName'];
    }

    return $result;
}

// [호텔] 호텔의 등급 정보를 가져옴
function getHotelGrade($AccomIdx)
{

    $pdo = pdoSqlConnect();
    $query = "
select HotelGrade.AccomGrade,
Authentication
from HotelGrade
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

