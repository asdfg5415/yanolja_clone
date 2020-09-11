<?php



function myYanolja($UserId, $UserIdx)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT 
    UserName,
    User.UserIdx,
    User.UserPoint,
    CASE 
		WHEN
			(SELECT EXISTS(
					SELECT *     
					FROM UserCoupon join Coupon Using (CouponIdx)
					WHERE UserCoupon.UserIdx = ?) = 1)
		THEN
			(SELECT
				COUNT(UserCoupon.CouponIdx)
			FROM
				UserCoupon Join Coupon using (CouponIdx)
			WHERE
				DATE(Coupon.EndDate) >= DATE(NOW())
				AND Coupon.isDeleted = 'N'
				AND UserCoupon.UserIdx = ?)
		ELSE
			0
	END as CouponCount
FROM
    User
        JOIN
    (UserCoupon
    JOIN Coupon USING (CouponIdx))
WHERE
	User.UserId = ?
    Limit 1;";

    $st = $pdo->prepare($query);
    $st->execute([$UserIdx, $UserIdx, $UserId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getUserIdx($UserId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT UserIdx FROM User Where UserId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$UserId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['UserIdx'];
}

function isValidPwd($UserId, $UserPwd)
{
    $pdo = pdoSqlConnect();
    $query = "select exists (
select UserID from User 
where UserId = ?
and UserPwd = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$UserId, $UserPwd]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]['exist']);
}

function isValidMotel($AccomIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select exists (
select AccomIdx from Accommodation 
where AccomIdx = ? AND AccomType = 'M') as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]['exist']);
}

function isValidHotel($AccomIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select exists (
select AccomIdx from Accommodation 
where AccomIdx = ? AND AccomType = 'H') as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]['exist']);
}

function isValidAccom($AccomIdx)
{
    $pdo = pdoSqlConnect();
    $query = "select exists (
select AccomIdx from Accommodation 
where AccomIdx = ?) as exist;";

    $st = $pdo->prepare($query);
    $st->execute([$AccomIdx]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]['exist']);
}

function getUserInfo($UserId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT UserName, UserId, UserPwd, UserContact
    FROM User WHERE UserId = ?";

    $st = $pdo->prepare($query);
    $st->execute([$UserId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function getUserReservation($UserId)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT count(ReserveIdx) as DomesticAccommodation
FROM 
	User Join Reservation using (UserIdx)
WHERE
	User.UserId = ?
    AND TIMESTAMPDIFF(Month, Reservation.createdAt, NOW()) < 3;";

    $st = $pdo->prepare($query);
    $st->execute([$UserId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function isValidName($UserName)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS( SELECT UserName from User where UserName = ?) as exist";

    $st = $pdo->prepare($query);
    $st->execute([$UserName]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return intval($res[0]['exist']);
}

function patchUserName($UserId, $UserName){
    $pdo = pdoSqlConnect();
    $query = "UPDATE User SET UserName = ? 
WHERE (UserId = ?);";

    $st = $pdo->prepare($query);
    $st->execute([$UserName, $UserId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;
}

function patchUserPwd($UserId, $UserPwd){
    $pdo = pdoSqlConnect();
    $query = "UPDATE User SET UserPwd = ? 
WHERE (UserId = ?);";

    $st = $pdo->prepare($query);
    $st->execute([$UserPwd, $UserId]);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;
}
// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }
