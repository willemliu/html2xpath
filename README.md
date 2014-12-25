html2xpath
==========

Traverse given URL by XPath and returns selected element as JSON.

## Usage
Note that IMDB information is protected by Copyright. The following is for educational purposes only.

Retrieve IMDB Mobile ratings:
```
http://html2xpath/?u=http://m.imdb.com/title/tt2310332&x[0]=//div[contains(@id,'ratings-bar')]/div/span[2]&x[1]=//div[contains(@id,'ratings-bar')]/div/span[2]/small/text()[2]
```
will return:
```
{
    "0": {
        "attributes": [
            {
                "class": "inline-block text-left vertically-middle"
            }
        ],
        "textContent": [
            "7.9"
        ]
    },
    "1": {
        "textContent": [
            "91,217"
        ]
    },
    "exec_time": 0.87327790260315
}
```

Retrieve IMDB ratings:
```
http://html2xpath/?u=http://www.imdb.com/title/tt2310332&x[0]=//div[contains(@class,'star-box-giga-star')]&x[1]=//span[@itemprop='ratingCount']
```
will return:
```
{
    "0": {
        "attributes": [
            {
                "class": "titlePageSprite star-box-giga-star"
            }
        ],
        "textContent": [
            " 7.9 "
        ]
    },
    "1": {
        "attributes": [
            {
                "itemprop": "ratingCount"
            }
        ],
        "textContent": [
            "91,217"
        ]
    },
    "exec_time": 1.9000370502472
}
```
