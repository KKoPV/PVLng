DROP FUNCTION `pvlng_api_key`;

DELIMITER ;;

CREATE FUNCTION `pvlng_api_key` () RETURNS varchar(36) CHARACTER SET 'utf8'
BEGIN
    SELECT `value` INTO @KEY FROM `pvlng_config` WHERE `key` = 'APIKey';
    IF @KEY IS NULL THEN
        SET @KEY = MD5(MD5(UUID()));
        INSERT INTO `pvlng_config`
                    (`key`, `value`, `comment`)
             VALUES ('APIKey', @KEY, 'API key for all PUT/POST/DELETE requests');
    END IF;
    RETURN @KEY;
END;;

DELIMITER ;
