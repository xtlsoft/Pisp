(do
	(@def loginURL ["/login"])
	(router
		(group /user_center
			(GET /{username}.html &UserController:showUserInfomation)
			(group /auth
				(GET /login.html &UserController:userLogin)
				(GET /register.html &UserController:userRegister)
				(group /backend
					(POST (loginURL) &UserController:handleLogin)
					(POST /register &UserController:handleRegister)
				)
			)
		)
		(GET /index.html &IndexController:index)
	)
)
