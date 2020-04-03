# Amrita Repository - Bot
> A non-modular, low-perf, no-ML, if-else telegram bot built with Flight PHP.

[![Bot Link](https://img.shields.io/badge/Telegram-@amrepobot-333e5c)](https://t.me/amrepobot) 

## Contributing
- Make sure you have `PHP` and `composer` installed in your system.
- Create a Telegram bot with the help of [Botfather](https://t.me/botfather).
- Know your own Telegram ID with the help of a bot like [this](https://t.me/chatid_echo_bot) one.
- **Fork** this repository.
- Clone it into your local machine.
```bash
git clone https://github.com/<YOUR_USERNAME>/amritarepo-bot.git
```
- Get into the `amritarepo-bot` directory.
- Copy `config.example.php` and paste it as `config.php` and fill in necessary stuff.
- Create `access.log` and `error.log` at the root of the project directory.
- Run a `composer install` to fetch all dependencies.
- Run `php -S 0.0.0.0:2304` to start the web server.
- Use an API testing tool like [Postman](https://postman.com) and send the request to `http://localhost:2304/<YOUR_API_KEY>` with the sample body 
```json
{
  "message" : {
      "text" : "Whatever you want to send to the bot",
      "from" : {
          "id" : "<YOUR_TELEGRAM_ID>",
          "first_name" : "<YOUR_FIRST_NAME>",
          "username" : "<YOUR_TELEGRAM_USERNAME>"
      }
  }
}
```
- Tadaa! You'll get the response to your Telegram ^_^
- Make your awesome changes, push your changes into a **new branch**.
- Send in a pull-request :)

## Misc. Info
- If you run this on your system, question papers module won't work unless you're in Amrita LAN. As a workaround, you can use [SonicWall NetExtender](https://www.mysonicwall.com/muir/freedownloads) (Make sure you choose **`NetExtender`** in the dropdown before downloading) and create a connection using the below details :
```
  Connection Name : Amrita
  Server : 117.240.224.2:4433
  Username/Password : Your CMS/Amrita WiFi credentials
```

## Open Source License
Read the license [here](LICENSE)
