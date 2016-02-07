# MX QR Code#

![MX Google Map V1](images/mx-qr-code-expressionengine3)

**MX QR Code** QR Code Generator for ExpressionEngine 3.

## Installation
* Download the latest version of MX QR Code and extract the .zip to your desktop.
* Copy *qr_code* to */system/user/addons/*

## Configuration
Once the Plugin is installed, you should be able to see it listed in the Add-On Manager in your ExpressionEngine Control Panel. Is no needs in any control panel activation or configuration.

## Template Tags

{exp:qr_code}

	<img src="{exp:qr_code type="" action="" tel="" data="ExpressionEngine" title="" email="" subj=""}" alt="QR code"/> 

### Parameters

	expression="-4(15/42)^23*(4-sqrt(16))-15" required

### Avalible functions:
*action = "" *optional

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

*size = "4"* optional, default "4"

module size

*ecc = "M"* optional, default **M**

ECC level **L** or **M** or **Q** or **H**

*data = ""*

default data for encoding

*px_color = "000000"* optional, default **000000**

pixel color

*bk_color = "ffffff"* optional, default **ffffff**

background color

*outline_size = "2"* optional, default **2**

outline size

*base_path*

path to web root (required if you use base_cache parameter)

*base_cache* optional, default "/images/cache/"

path to image folder

## Support Policy
This is Communite Edition (CE) add-on.

## Contributing To MX QR Code for ExpressionEngine 3

Your participation to MX QR Code development is very welcome!

You may participate in the following ways:

* [Report issues](https://github.com/MaxLazar/mx-qr-code-ee3/issues)
* Fix issues, develop features, write/polish documentation
Before you start, please adopt an existing issue (labelled with "ready for adoption") or start a new one to avoid duplicated efforts.
Please submit a merge request after you finish development.


## License

The MX QR Code is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)