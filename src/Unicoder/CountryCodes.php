<?php

declare(strict_types=1);

namespace Unicoder;
class CountryCodes {

    private static $codes = [
        "AF" => "Afghanistan",
        "AZ" => "Azerbaijan",
        "BD" => "Bangladesh",
        "BT" => "Bhutan",
        "BN" => "Brunei",
        "KH" => "Cambodia",
        "CN" => "China",
        "IN" => "India",
        "ID" => "Indonesia",
        "JP" => "Japan",
        "KZ" => "Kazakhstan",
        "KG" => "Kyrgyzstan",
        "LA" => "Laos",
        "MY" => "Malaysia",
        "MV" => "Maldives",
        "MN" => "Mongolia",
        "MM" => "Myanmar",
        "NP" => "Nepal",
        "KP" => "North Korea",
        "PK" => "Pakistan",
        "PH" => "Philippines",
        "SG" => "Singapore",
        "KR" => "South Korea",
        "LK" => "Sri Lanka",
        "TW" => "Taiwan",
        "TJ" => "Tajikistan",
        "TH" => "Thailand",
        "TL" => "Timor-Leste",
        "TM" => "Turkmenistan",
        "UZ" => "Uzbekistan",
        "VN" => "Vietnam",
        "DZ" => "Algeria",
        "AO" => "Angola",
        "BJ" => "Benin",
        "BW" => "Botswana",
        "BF" => "Burkina Faso",
        "BI" => "Burundi",
        "CV" => "Cape Verde",
        "CM" => "Cameroon",
        "CF" => "Central African Republic",
        "TD" => "Chad",
        "KM" => "Comoros",
        "CD" => "Democratic Republic of the Congo",
        "CG" => "Republic of the Congo",
        "CI" => "Cote d'Ivoire",
        "DJ" => "Djibouti",
        "GQ" => "Equatorial Guinea",
        "ER" => "Eritrea",
        "ET" => "Ethiopia",
        "GA" => "Gabon",
        "GM" => "Gambia",
        "GH" => "Ghana",
        "GN" => "Guinea",
        "GW" => "Guinea-Bissau",
        "KE" => "Kenya",
        "LS" => "Lesotho",
        "LR" => "Liberia",
        "LY" => "Libya",
        "MG" => "Madagascar",
        "MW" => "Malawi",
        "ML" => "Mali",
        "MR" => "Mauritania",
        "MU" => "Mauritius",
        "MA" => "Morocco",
        "MZ" => "Mozambique",
        "NA" => "Namibia",
        "NE" => "Niger",
        "NG" => "Nigeria",
        "RW" => "Rwanda",
        "ST" => "Sao Tome and Principe",
        "SN" => "Senegal",
        "SC" => "Seychelles",
        "SL" => "Sierra Leone",
        "SO" => "Somalia",
        "ZA" => "South Africa",
        "SS" => "South Sudan",
        "SD" => "Sudan",
        "SZ" => "Swaziland",
        "TZ" => "Tanzania",
        "TG" => "Togo",
        "TN" => "Tunisia",
        "UG" => "Uganda",
        "EH" => "Western Sahara",
        "ZM" => "Zambia",
        "ZW" => "Zimbabwe",
        "AL" => "Albania",
        "AD" => "Andorra",
        "AM" => "Armenia",
        "AT" => "Austria",
        "BY" => "Belarus",
        "BE" => "Belgium",
        "BA" => "Bosnia and Herzegovina",
        "BG" => "Bulgaria",
        "HR" => "Croatia",
        "CY" => "Cyprus",
        "CZ" => "Czech Republic",
        "DK" => "Denmark",
        "EE" => "Estonia",
        "FI" => "Finland",
        "FR" => "France",
        "GE" => "Georgia",
        "DE" => "Germany",
        "GI" => "Gibraltar",
        "GR" => "Greece",
        "HU" => "Hungary",
        "IS" => "Iceland",
        "IE" => "Ireland",
        "IT" => "Italy",
        "LV" => "Latvia",
        "LI" => "Liechtenstein",
        "LT" => "Lithuania",
        "LU" => "Luxembourg",
        "MK" => "Macedonia",
        "MT" => "Malta",
        "MD" => "Moldova",
        "MC" => "Monaco",
        "ME" => "Montenegro",
        "NL" => "Netherlands",
        "NO" => "Norway",
        "PL" => "Poland",
        "PT" => "Portugal",
        "RO" => "Romania",
        "RU" => "Russian Federation",
        "SM" => "San Marino",
        "RS" => "Serbia",
        "SK" => "Slovakia",
        "SI" => "Slovenia",
        "ES" => "Spain",
        "SE" => "Sweden",
        "CH" => "Switzerland",
        "TR" => "Turkey",
        "GG" => "Guernsey",
        "JE" => "Jersey",
        "IM" => "Isle of Man",
        "UA" => "Ukraine",
        "GB" => "United Kingdom",
        "VA" => "Vatican City State",
        "BH" => "Bahrain",
        "EG" => "Egypt",
        "IR" => "Iran",
        "IQ" => "Iraq",
        "IL" => "Israel",
        "JO" => "Jordan",
        "KW" => "Kuwait",
        "LB" => "Lebanon",
        "OM" => "Oman",
        "PS" => "Palestine",
        "QA" => "Qatar",
        "SA" => "Saudi Arabia",
        "SY" => "Syria",
        "AE" => "United Arab Emirates",
        "YE" => "Yemen",
        "AG" => "Antigua and Barbuda",
        "BS" => "Bahamas",
        "BB" => "Barbados",
        "BZ" => "Belize",
        "CA" => "Canada",
        "CR" => "Costa Rica",
        "CU" => "Cuba",
        "DM" => "Dominica",
        "DO" => "Dominican Republic",
        "SV" => "El Salvador",
        "GD" => "Grenada",
        "GT" => "Guatemala",
        "HT" => "Haiti",
        "HN" => "Honduras",
        "JM" => "Jamaica",
        "MX" => "Mexico",
        "NI" => "Nicaragua",
        "PA" => "Panama",
        "KN" => "Saint Kitts and Nevis",
        "LC" => "Saint Lucia",
        "VC" => "Saint Vincent and the Grenadines",
        "TT" => "Trinidad and Tobago",
        "US" => "United States of America",
        "AU" => "Australia",
        "CK" => "Cook Islands",
        "FM" => "Federated Islands of Micronesia",
        "FJ" => "Fiji",
        "PF" => "French Polynesia",
        "GU" => "Guam",
        "KI" => "Kiribati",
        "MH" => "Marshall Islands",
        "NR" => "Nauru",
        "NZ" => "New Zealand",
        "PW" => "Palau",
        "PG" => "Papua New Guinea",
        "WS" => "Samoa",
        "SB" => "Solomon Islands",
        "TO" => "Tonga",
        "TV" => "Tuvalu",
        "VU" => "Vanuatu",
        "AR" => "Argentina",
        "BO" => "Bolivia",
        "BR" => "Brazil",
        "CL" => "Chile",
        "CO" => "Colombia",
        "EC" => "Ecuador",
        "GY" => "Guyana",
        "PY" => "Paraguay",
        "PE" => "Peru",
        "SR" => "Suriname",
        "UY" => "Uruguay",
        "VE" => "Venezuela",
        "CW" => "Curaçao",
        "BQ" => "Bonaire, Sint Eustatius, and Saba",
        "SX" => "Sint Maarten"
    ];

    const string FLAG_CHECKED = "🏁";
    const string FLAG_CROSSED = "🎌";

    const string FLAG_TRIANGLE = "🚩";

    const string FLAG_WHITE = "🏳️";
    const string FLAG_RAINBOW = "🌈 ";
    const string FLAG_BLACK = "🏴";

    const string FLAG_PIRATE = "‍☠️";

    private static ?array $sortedCodes = null;

    private static ?array $sortedCountries = null;

    /**
     * @return array
     */
    public static function getCountiesByCode(): array
    {
        if (!self::$sortedCodes) {
            self::$sortedCodes = self::$codes;
            asort(self::$sortedCodes);
        }
        return self::$sortedCodes;
    }

    /**
     * @return array
     */
    public static function getCodesByCountry(): array
    {
        if (!self::$sortedCountries) {
            self::$sortedCountries = array_flip(self::$codes);
            asort(self::$sortedCountries);

        }
        return self::$sortedCountries;
    }

    /**
     * @param $countryCode
     * @return string
     */
    public static function isoToUnicodeFlag($countryCode): string
    {
        // Ensure the country code is uppercase
        $countryCode = strtoupper($countryCode);
        // Convert each letter to the corresponding Regional Indicator Symbol
        $flag = '';
        foreach (str_split($countryCode) as $char) {
            // Convert the character to its position in the alphabet and get the Unicode flag
            $flag .= mb_chr(ord($char) - ord('A') + 0x1F1E6);
        }
        return $flag;
    }

    /**
     * @return array
     */
    public static function getUnicodeSet(): array
    {
        $codesByCountry = self::getCodesByCountry();
        $set=[];
        foreach ($codesByCountry as $code) {
            $set[] = self::isoToUnicodeFlag($code);
        }
        return $set;
    }
}