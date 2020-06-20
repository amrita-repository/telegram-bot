# Amrita Repository - Bot
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-3-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->
> A non-modular, low-perf, no-ML, if-else telegram bot built with Flight PHP.

[![Bot Link](https://img.shields.io/badge/Telegram-@amrepobot-informational)](https://t.me/amrepobot) ![Amrita Repository Bot CD](https://github.com/rajkumaar23/amritarepo-bot/workflows/Amrita%20Repository%20Bot%20CD/badge.svg)

## Contributing
- Make sure you have `PHP` and `composer` installed in your system.
- Create a Telegram bot with the help of [Botfather](https://t.me/botfather) and then `start` the bot.
- Know your own Telegram ID with the help of a bot like [this](https://t.me/chatid_echo_bot) one.
- **Fork** this repository.
- Clone it into your local machine.
```bash
git clone https://github.com/<YOUR_USERNAME>/amritarepo-bot.git
```
- Get into the `amritarepo-bot` directory.
- Copy `config.example.php` and paste it as `config.php` and fill in necessary stuff.
- Create `access.log` and `error.log` inside `logs` directory, with all permissions (`chmod 777 access.log error.log` on Linux).
- Run a `composer install` to fetch all dependencies.
- Run `php -S 0.0.0.0:2304` to start the web server.
- Use an API testing tool like [Postman](https://postman.com) and send a POST request to `http://localhost:2304/<YOUR_BOT_TOKEN>` with the sample body
```json
{
  "message" : {
      "text" : "/start",
      "from" : {
          "id" : "<YOUR_TELEGRAM_ID>",
          "first_name" : "<YOUR_FIRST_NAME>",
          "username" : "<YOUR_TELEGRAM_USERNAME>"
      }
  }
}
```
##### Example
![Postman](postman.png?raw=true)

- If you get an error like `Chat not found`, it usually means that you have not **started** the bot from your personal Telegram account. Go to the bot and **START** it.
- Tadaa! You'll get the response to your Telegram chat ^_^
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

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="https://rajkumaar.co.in"><img src="https://avatars1.githubusercontent.com/u/37476886?v=4" width="100px;" alt=""/><br /><sub><b>Rajkumar S</b></sub></a><br /><a href="https://github.com/rajkumaar23/amritarepo-bot/commits?author=rajkumaar23" title="Code">💻</a> <a href="https://github.com/rajkumaar23/amritarepo-bot/commits?author=rajkumaar23" title="Documentation">📖</a></td>
    <td align="center"><a href="https://github.com/sanjana2610"><img src="https://avatars3.githubusercontent.com/u/41717568?v=4" width="100px;" alt=""/><br /><sub><b>Sanjana G</b></sub></a><br /><a href="https://github.com/rajkumaar23/amritarepo-bot/commits?author=sanjana2610" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/ArunVigesh"><img src="https://avatars2.githubusercontent.com/u/36444828?v=4" width="100px;" alt=""/><br /><sub><b>Arun Vigesh V G</b></sub></a><br /><a href="https://github.com/rajkumaar23/amritarepo-bot/commits?author=ArunVigesh" title="Code">💻</a></td>
  </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!
