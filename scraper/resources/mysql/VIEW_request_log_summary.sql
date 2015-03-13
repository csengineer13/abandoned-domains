-- http://stackoverflow.com/questions/2279240/how-can-i-edit-a-view-using-phpmyadmin-3-2-4

SELECT  
	users.Id, 
	users.FirstName, 
	users.LastName, 
	users.Email, 
	COUNT(CASE WHEN logs.LoggedDate > timestampadd(day, -1, now()) THEN 1 ELSE NULL END) AS FailedToday, 
	COUNT(CASE WHEN logs.LoggedDate > timestampadd(day, -30, now()) THEN 1 ELSE NULL END) AS FailedMonth, 
	COUNT(logs.From) AS FailedAllTime, 
	users.IsBanned
FROM  abd_users users
LEFT JOIN abd_request_log logs ON logs.From = users.Email AND logs.F_IsInvalid = 1
GROUP BY users.Email
ORDER BY users.Id