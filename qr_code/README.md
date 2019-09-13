# MX QR Code#

**MX QR Code** QR Code Generator for ExpressionEngine.

## Installation
* Place the **qr_code** folder inside your **user/addons** folder
* Go to **cp/addons** and install *MX QR Code*

## Template Tags

{exp:qr_code}

	<img src="{exp:qr_code action="" tel="" data="ExpressionEngine" title="" email="" subj=""}" alt="QR code"/>

### Parameters

*action = ""* optional

Plugin has a couple pattern for QR code messages:

* sms - send sms
* tel - phone number
* email - send email
* bm - add to bookmarks
* site - addtional data urlencode

*tel = "555-5555"* required for action type sms,tel

telephone number

*email = "name@name.com" *required for action type email

email

*subj = "John Doe"* required for action type email

subject for email

*title = "My WebSite"* required for action type bookmarks

Title for bookmark

*size = "200"* optional, default "200"

qr code size *in px*

*ecc = "M"* optional, default **M**

ECC level **L** or **M** or **Q** or **H**

*data = ""*

default data for encoding

*px_color = "000000"* optional, default **000000**

pixel color

*bk_color = "ffffff"* optional, default **ffffff**

background color

*margin = "2"* optional, default **0**

margin size in px

*logo = ""* optional

url / path to logo


*logo_size="100,100"* optional

logo size


*base_path*

path to web root (required if you use base_cache parameter)

*base_cache* optional, default "/images/cache/"

path to image folder

*base64_encode = "no"* optional

incode image to base64 to inline image


## Support Policy
This is Communite Edition (CE) add-on.

## Contributing To MX QR Code for ExpressionEngine

Your participation to MX QR Code development is very welcome!

You may participate in the following ways:

* [Report issues](https://github.com/MaxLazar/mx-qr-code-ee3/issues)
* Fix issues, develop features, write/polish documentation
Before you start, please adopt an existing issue (labelled with "ready for adoption") or start a new one to avoid duplicated efforts.
Please submit a merge request after you finish development.


## License

The MX QR Code is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
