#### The basics
- Type `/point @username` to give someone a point
- Type `/points` to see the leaderboard
- Add `private` at the end to make it anonymous ie `/point @username private` or `/points private`
- You can only give one point every 30 minutes (thinking maybe this should be less maybe 5 minutes but let’s see what everyone thinks)
- You can’t give yourself points

#### Possible future features
- Username validation. There is no validation on the username you type so if you get it wrong it will be assigned to a non-existing user and will still appear in the leaderboard. It’s possible to check the user via the Slack API but it slows the call down significantly. Best of both worlds would be to post validate the username or cache a team’s users via the API.
- Post the leaderboard periodically for everyone to see

#### Setup
- Complete all properties in config.php marked as required
- Run SQL script to create database
- Add your team to the teams table
- Add the slash command integration to your team
