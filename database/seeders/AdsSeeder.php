<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Ad;
use App\Models\Category;

class AdsSeeder extends Seeder
{

    use WithoutModelEvents;
    public function run(): void
    {
        $faker = Faker::create();

        // Assuming you have 10 users and a range of sub-categories.
        $userIds = User::pluck('id');

        // Your ads template structure

        $adsTemplates = [
            "Mobile Phones" => [
                "Smartphones" => [
                    [
                        'title' => 'Brand New Sealed 5G Smartphone',
                        'description' => "Selling my brand new 5G smartphone - sealed and unopened.\n- Received as a gift but already have a phone\n- Latest 2023 Model with warranty\n- Crystal clear camera, perfect for photography\n- Price slightly negotiable\n- Serious buyers only, please."
                    ],

                    [
                        'title' => 'Preloved Compact Smartphone',
                        'description' => "Looking to sell my compact smartphone.\n- Lightly used, in perfect condition\n- Great for people who prefer smaller screens\n- No scratches or dents\n- Comes with original box and charger\n- Selling because I upgraded to a new phone."
                    ],

                    [
                        'title' => 'Pro Smartphone with Advanced Camera',
                        'description' => "Offering my Pro smartphone with an advanced camera system.\n- Used for 6 months, no issues\n- Takes fantastic photos, even in low light\n- Comes with a protective case and screen protector\n- Reason for selling: Got it from my office, but prefer iOS.\n- Meet-up preferred for serious buyers."
                    ],

                    [
                        'title' => 'Budget Smartphone (Great Deal!)',
                        'description' => "Hey everyone, selling a budget-friendly smartphone.\n- Perfect for students or as a secondary phone\n- Dual SIM support\n- Some minor signs of use, but works perfectly\n- Price is a steal for the features it offers\n- Can show the phone via video call before meet-up."
                    ]
                ],

                "Feature Phones" => [
                    [
                        'title' => 'Classic Feature Phone (Old-school)',
                        'description' => "Going old school? I have a classic feature phone for sale.\n- Super reliable with amazing battery life\n- Ideal for calls and texts only\n- Keypad is smooth and easy to use\n- Nostalgia overload!\n- Price fixed. Those who appreciate it will know its value."
                    ],

                    [
                        'title' => 'Rugged Feature Phone (For Adventures)',
                        'description' => "Selling a rugged feature phone.\n- Built like a tank. Perfect for adventures and travels\n- Can survive drops, water splashes, and dust\n- Torchlight feature which is super bright\n- Selling as I've moved to a smartphone.\n- Grab this before your next adventure."
                    ],

                    [
                        'title' => 'Multimedia Feature Phone (Music, Radio)',
                        'description' => "Hey, got a multimedia feature phone here.\n- Perfect for those who love music and radio on the go\n- Expandable storage: load up all your songs\n- Simple and user-friendly interface\n- Selling as it's an extra phone I don't use.\n- Hoping it finds a new home."
                    ],

                    [
                        'title' => 'Feature Phone (Elderly-Friendly)',
                        'description' => "Feature phone for sale, elderly-friendly.\n- Large buttons, loud and clear speaker\n- Simple to use, no complications\n- Got it for my grandpa, but he's moved to a smartphone\n- Ideal for seniors or those who want simplicity.\n- Contact if interested. No time-wasters."
                    ]
                ],
                "Mobile Accessories" => [
                    [
                        'title' => 'Premium Smartphone Case (Leather)',
                        'description' => "Selling a premium leather smartphone case.\n- Genuine leather with a luxurious feel\n- Provides full protection to the phone\n- Unused and in pristine condition\n- Compatible with most recent smartphone models\n- Price is a bit negotiable. DM if interested."
                    ],

                    [
                        'title' => 'Fast Wireless Charger (15W)',
                        'description' => "Offering a fast wireless charger for sale.\n- Supports up to 15W fast charging\n- Non-slip design, compact and portable\n- Used for a month, selling as I received another one as a gift\n- Compatible with all Qi-enabled devices\n- Safe and reliable. Don’t miss out on this deal."
                    ],

                    [
                        'title' => 'Noise-Cancelling Earbuds',
                        'description' => "Looking to sell my noise-cancelling earbuds.\n- Active noise-cancellation for immersive audio\n- Comes with a portable charging case\n- Barely used, almost like new\n- Selling because I prefer over-ear headphones\n- Contact for a quick demo and deal."
                    ],

                    [
                        'title' => 'Tempered Glass Screen Protector',
                        'description' => "Got a few tempered glass screen protectors for sale.\n- High-quality, provides full screen protection\n- Prevents scratches and damage\n- Easy to apply, bubble-free\n- Fits most current smartphone models\n- Discount if you buy more than one. Hit me up!"
                    ]
                ],

                "SIM Cards" => [
                    [
                        'title' => 'Prepaid SIM with High-speed Data',
                        'description' => "Offering a prepaid SIM card with high-speed data.\n- Great for tourists or short-term visitors\n- Comes with 10GB data and unlimited local calls\n- Valid for 30 days from activation\n- Easy activation process\n- Grab this SIM and stay connected on the go."
                    ],

                    [
                        'title' => 'International Roaming SIM (Multiple Countries)',
                        'description' => "Got an international roaming SIM for those who travel.\n- Works in over 50 countries\n- Competitive calling and data rates\n- No need to switch SIMs as you travel\n- Valid for 60 days\n- Travel without the hassle of finding local SIM cards."
                    ],

                    [
                        'title' => 'Postpaid SIM (Great Monthly Plan)',
                        'description' => "Selling a postpaid SIM with an affordable monthly plan.\n- Unlimited calls, SMS, and 30GB high-speed data\n- Comes with free OTT platform subscriptions\n- No long-term commitment required\n- Easy to transfer ownership\n- Message me for more details on the plan."
                    ],

                    [
                        'title' => 'eSIM Activation Voucher',
                        'description' => "Got an eSIM activation voucher up for grabs.\n- Go digital with an electronic SIM\n- No physical SIM card needed\n- Easy to set up and use\n- Works with compatible smartphones and devices\n- Experience the future of connectivity with eSIM."
                    ]
                ],
            ],
            "Bikes" => [

                "Sports Bikes" => [
                    [
                        'title' => '2022 Raptor ZX Sports Bike',
                        'description' => "**2022 Raptor ZX Sports Bike**\n- 600cc powerful engine\n- Only 2,500 miles driven\n- Dual ABS system & slipper clutch\n- Racing red color, showroom condition\n- All services done from authorized center\n- Racing exhaust installed for enhanced sound\n- Paperwork all clear. Reach out for a test ride."
                    ],

                    [
                        'title' => 'Tornado X5 Sports Edition',
                        'description' => "**Tornado X5 Sports Edition**\n- Smooth 750cc engine performance\n- Minimalistic design with aggressive looks\n- Driven just 1,500 miles\n- First owner and well-maintained\n- Full insurance covered with no accident history\n- Selling to upgrade. Serious buyers only."
                    ],

                    [
                        'title' => 'Knight Racer 500 - Racing Blue',
                        'description' => "**Knight Racer 500**\n- Sleek racing blue color\n- 500cc beast with great mileage\n- Always garaged and regularly serviced\n- Racing tires with great grip\n- A dream for racing enthusiasts\n- Ready for immediate sale. Contact for more pictures."
                    ],

                    [
                        'title' => 'ThunderSport TX - Limited Edition',
                        'description' => "**ThunderSport TX - Limited Edition**\n- Only 5,000 units made worldwide\n- A collector's item\n- Supercharged 800cc engine\n- Custom leather seats and golden accents\n- All original parts and documents available\n- A real head-turner on the road. DM for inquiries."
                    ]
                ],

                "Scooters" => [
                    [
                        'title' => 'Electra Glide Scooter - White',
                        'description' => "**Electra Glide Scooter**\n- Smooth automatic transmission\n- Comfortable for daily commutes\n- White color, single owner\n- 12,000 miles on the clock\n- Good storage space under the seat\n- Efficient fuel economy. Contact to check out."
                    ],

                    [
                        'title' => '2022 UrbanRide - Compact Scooter',
                        'description' => "**2022 UrbanRide Compact Scooter**\n- Perfect for city rides and errands\n- Just 5,000 miles driven\n- Spacious boot storage\n- Automatic headlights and digital meter\n- All services up-to-date\n- Reason for sale: Moving out of town."
                    ],

                    [
                        'title' => 'Swiftly Classic - Vintage Green',
                        'description' => "**Swiftly Classic Scooter**\n- Vintage green color, classic design\n- Always a crowd favorite\n- Ideal for short rides in style\n- Comes with a protective cover\n- Kept in prime condition over the years\n- A blend of classic and modern. Don't miss out!"
                    ],

                    [
                        'title' => 'MoonRider - Latest Model',
                        'description' => "**MoonRider Scooter - Latest Model**\n- Efficient and silent electric motor\n- USB charging port available\n- Touchscreen control panel\n- Up to 60 miles on a single charge\n- Green and eco-friendly ride\n- Selling due to upgrade. Drop a message for more."
                    ]
                ],

                "Cruiser Bikes" => [
                    [
                        'title' => 'Majestic Cruiser 1100 - Sunset Orange',
                        'description' => "**Majestic Cruiser 1100**\n- Perfect bike for long rides\n- Powerful 1100cc engine\n- Sunset orange color, chrome finish\n- Premium leather seats\n- Sound system with Bluetooth connectivity\n- Well-maintained and all services done on time\n- Ready for the highways. Reach out for more details."
                    ],

                    [
                        'title' => '2023 Titan Glide - Black Beauty',
                        'description' => "**2023 Titan Glide Cruiser**\n- Ultimate comfort with wide seats\n- Strong 950cc engine\n- Jet black with matte finish\n- LED lights and modern tech\n- Only 4,000 miles driven\n- A true beast on the road. Inquiries welcome."
                    ],

                    [
                        'title' => 'Desert Rider 900 - Sand Gold',
                        'description' => "**Desert Rider 900**\n- Unique sand gold color\n- Wide tires, ideal for all terrains\n- Sturdy 900cc engine\n- Comes with saddlebags\n- High handlebars for comfortable riding posture\n- Dream bike for all cruiser enthusiasts. Contact for a test ride."
                    ],

                    [
                        'title' => '2022 OceanWave - Metallic Blue',
                        'description' => "**2022 OceanWave Cruiser**\n- Mesmerizing metallic blue shade\n- Cruise control for easy rides\n- Advanced suspension system\n- Amplified sound system for quality music\n- Turning heads wherever you go\n- Immediate sale due to relocation. DM for inquiries."
                    ]
                ],

                "Bike Accessories" => [
                    [
                        'title' => 'Riding Jacket - Weatherproof',
                        'description' => "**Weatherproof Riding Jacket**\n- Provides protection in all weather conditions\n- Built-in armor for safety\n- Reflective strips for night visibility\n- Multiple pockets for storage\n- Brand new, never used. Grab this deal today."
                    ],

                    [
                        'title' => 'Motorcycle Helmet - Racing Red',
                        'description' => "**Motorcycle Helmet - Racing Red**\n- DOT certified for maximum safety\n- Clear visor with anti-scratch coating\n- Comfortable inner padding\n- Ventilation system for airflow\n- Perfect for city or highway rides\n- Safety first! Contact for more pictures."
                    ],

                    [
                        'title' => 'Grip Handlebar Covers',
                        'description' => "**Grip Handlebar Covers**\n- Increases grip and comfort\n- Durable rubber material\n- Easy installation without tools\n- Suitable for most bike models\n- Enhances the overall look of your bike\n- Set of 2. Ready for immediate shipment."
                    ],

                    [
                        'title' => 'LED Light Kit for Bikes',
                        'description' => "**LED Light Kit for Motorcycles**\n- Brightens up the road for safe night rides\n- Energy efficient and long-lasting\n- Easy to install with the included manual\n- Suitable for most bike models\n- Light up the way and ride with confidence."
                    ]
                ],
            ],

            "Electronics & Appliances" => [

                "Home Appliances" => [
                    [
                        'title' => '2023 Dyson V12 Vacuum Cleaner',
                        'description' => "**Dyson V12 Vacuum Cleaner**\n- Ultra-powerful suction\n- Cordless design for ease of use\n- Complete set with all attachments\n- Battery lasts up to 2 hours\n- Perfect for home and car cleaning\n- Barely used, almost new. Contact for a demo."
                    ],

                    [
                        'title' => 'Samsung Inverter Air Conditioner',
                        'description' => "**Samsung Inverter AC - 1.5 Ton**\n- Energy efficient with 5-star rating\n- Fast cooling and silent operation\n- Comes with remote and stabilizer\n- Regularly serviced and well-maintained\n- Selling due to relocation. Great deal for summers!"
                    ],

                    [
                        'title' => 'LG Front Load Washing Machine',
                        'description' => "**LG Front Load Washing Machine - 7 Kg**\n- Multiple wash programs for different fabrics\n- Gentle on clothes, tough on stains\n- Energy and water efficient design\n- Inbuilt heater for winter washes\n- Compact design suitable for apartments\n- In warranty. Don't miss out on this offer."
                    ],

                    [
                        'title' => '2023 Whirlpool Refrigerator',
                        'description' => "**Whirlpool Double Door Refrigerator - 350L**\n- FreshFlow air tower for uniform cooling\n- Large vegetable crisper\n- Frost-free with auto defrost function\n- IceTwister and collector for easy ice collection\n- Eco mode for energy saving\n- Upgrading to a bigger size. Contact for more details."
                    ]
                ],

                "Kitchen Appliances" => [
                    [
                        'title' => 'Instant Pot - Multi-functional Cooker',
                        'description' => "**Instant Pot - 8L**\n- Multi-functional: Pressure cooker, slow cooker, rice cooker, and more\n- Stainless steel inner pot\n- Comes with recipe book and accessories\n- Touch screen control panel\n- Barely used, in perfect condition\n- Simplify your kitchen tasks with this one appliance."
                    ],

                    [
                        'title' => 'Nespresso Coffee Machine',
                        'description' => "**Nespresso Coffee Machine - Latest Model**\n- One-touch brewing system\n- Uses coffee pods for quick preparation\n- Milk frother included\n- Compact design, perfect for small kitchens\n- Enjoy barista-style coffee at home\n- Hurry up, coffee lovers!"
                    ],

                    [
                        'title' => 'Breville Smart Oven Air Fryer',
                        'description' => "**Breville Smart Oven with Air Fryer Functionality**\n- Multiple cooking functions: bake, broil, roast, air fry\n- Large capacity fits a whole chicken\n- Digital display and timer\n- Convection powered for fast and even cooking\n- Recipe book included\n- A must-have for modern kitchens. DM for inquiries."
                    ],

                    [
                        'title' => 'Philips Juicer Mixer Grinder',
                        'description' => "**Philips Juicer Mixer Grinder - 3 Jars**\n- Powerful 700W motor\n- Stainless steel blades\n- Easy to clean and maintain\n- Includes juicer, grinder, and blender functions\n- Perfect for smoothies, chutneys, and juices\n- Selling due to upgrade. Grab this deal today."
                    ]
                ],

                "Computers & Laptops" => [
                    [
                        'title' => 'Apple MacBook Pro M2 - 2023 Model',
                        'description' => "**Apple MacBook Pro M2 - 13-inch**\n- Latest M2 chip with enhanced performance\n- 16GB RAM, 512GB SSD storage\n- Retina display with True Tone\n- Touch Bar and Touch ID\n- Comes with original box and charger\n- Slightly used, no scratches or dents. Excellent condition."
                    ],

                    [
                        'title' => 'Dell XPS 15 with NVIDIA Graphics',
                        'description' => "**Dell XPS 15 - 2023 Edition**\n- 10th Gen Intel i7 processor\n- 32GB RAM, 1TB SSD storage\n- NVIDIA GTX 1650Ti graphics\n- InfinityEdge display, 4K resolution\n- Perfect for gaming and professional work\n- In warranty. Contact for a demo and pick-up."
                    ],

                    [
                        'title' => 'HP All-in-One Desktop Computer',
                        'description' => "**HP Pavilion All-in-One PC**\n- 27-inch Full HD touchscreen\n- Intel i5, 8GB RAM, 256GB SSD + 1TB HDD\n- Sleek and space-saving design\n- Comes with wireless keyboard and mouse\n- Suitable for home and office use\n- Priced to sell. Get in touch for details."
                    ],

                    [
                        'title' => 'Lenovo ThinkPad - Business Laptop',
                        'description' => "**Lenovo ThinkPad T-series**\n- Robust and durable design\n- Intel i7, 16GB RAM, 512GB SSD\n- Extended battery life, perfect for travel\n- Comes with Windows 11 Pro and MS Office\n- Ideal for business professionals on the go\n- Slightly negotiable. Message for inquiries."
                    ]
                ],

                "Cameras & Accessories" => [
                    [
                        'title' => 'Canon EOS R6 - Mirrorless Camera',
                        'description' => "**Canon EOS R6 - Full Frame Mirrorless**\n- 20.1MP, 4K video recording\n- Comes with 24-70mm f/2.8 lens\n- Dual card slots, WiFi and Bluetooth\n- Includes extra battery and bag\n- Perfect for photography enthusiasts and professionals\n- Sparingly used. Selling due to upgrade."
                    ],

                    [
                        'title' => 'Nikon D750 DSLR with Kit Lens',
                        'description' => "**Nikon D750 DSLR**\n- 24.3MP, Full frame sensor\n- Comes with 24-120mm f/4 kit lens\n- Excellent in low light situations\n- Full HD video recording\n- Includes camera bag, filters, and tripod\n- Well-maintained and in excellent condition."
                    ],

                    [
                        'title' => 'GoPro HERO10 - Action Camera',
                        'description' => "**GoPro HERO10 Black**\n- 5.3K video, 23MP photos\n- HyperSmooth 4.0 stabilization\n- Waterproof and rugged design\n- Perfect for travel, vlogging, and sports\n- Comes with mounts and accessories\n- Grab this deal for your next adventure."
                    ],

                    [
                        'title' => 'Sony Alpha a6400 - Mirrorless Camera',
                        'description' => "**Sony Alpha a6400 with 16-50mm Lens**\n- 24.2MP, 4K video\n- Fast autofocus, flip screen for vlogging\n- Compact design, great for travel\n- Includes extra battery and SD card\n- Excellent for both photos and videos\n- Slightly negotiable. Contact for more details."
                    ]
                ],

                "TV & Video" => [
                    [
                        'title' => 'Samsung 65-inch 4K Smart TV',
                        'description' => "**Samsung QLED 4K Smart TV - 65-inch**\n- Stunning 4K resolution with HDR\n- Smart features with built-in Netflix, Prime Video, etc.\n- Sleek and modern design\n- Comes with wall mount and stand\n- Voice remote included\n- Upgrade your entertainment experience. Get in touch now."
                    ],

                    [
                        'title' => 'LG OLED 55-inch TV with Dolby Vision',
                        'description' => "**LG OLED TV - 55-inch**\n- Perfect blacks with OLED technology\n- Dolby Vision and Atmos for superior quality\n- WebOS with popular streaming apps\n- ThinQ AI with voice recognition\n- Used for less than a year, in mint condition\n- Priced to sell. Contact for a quick deal."
                    ],

                    [
                        'title' => 'Sony Bravia 50-inch Full HD LED TV',
                        'description' => "**Sony Bravia LED TV - 50-inch**\n- Full HD resolution, X-Reality Pro engine\n- ClearAudio+ for enhanced sound\n- Multiple HDMI and USB ports\n- Comes with original remote and stand\n- Perfect for gaming and movies\n- Immediate sale. Serious buyers only."
                    ],

                    [
                        'title' => 'TCL Roku 43-inch Smart TV',
                        'description' => "**TCL Roku Smart TV - 43-inch**\n- Full HD resolution with vibrant colors\n- Built-in Roku for streaming\n- Voice control with Alexa and Google Assistant\n- Easy to set up and user-friendly\n- Suitable for bedrooms and small spaces\n- Almost new, used in the guest room. Great deal."
                    ]
                ],

                "Audio & Music Equipment" => [
                    [
                        'title' => 'Bose QuietComfort 45 Wireless Headphones',
                        'description' => "**Bose QuietComfort 45 Wireless Headphones**\n- Industry-leading noise cancellation\n- Up to 24 hours of battery life\n- Comfortable over-ear design\n- Supports Alexa and Google Assistant\n- Comes with original box and accessories\n- Mint condition. Get in touch for a demo."
                    ],

                    [
                        'title' => 'Yamaha HS8 Studio Monitor Pair',
                        'description' => "**Yamaha HS8 Studio Monitor Pair**\n- 8-inch cone woofer and 1-inch dome tweeter\n- Professional-grade sound quality\n- Used in home studios and professional setups\n- Comes with power cables\n- Ideal for music producers and sound engineers\n- Slightly negotiable. Serious inquiries only."
                    ],

                    [
                        'title' => 'Roland TD-17KVX Electronic Drum Kit',
                        'description' => "**Roland TD-17KVX Electronic Drum Kit**\n- Features mesh heads for all toms and snare\n- Bluetooth audio playback\n- Includes hi-hat, crash, and ride cymbals\n- Great for silent practice or recording\n- Comes with drum throne and sticks\n- Perfect for both beginners and pros. Get in touch for a deal."
                    ],

                    [
                        'title' => 'Audio-Technica AT-LP120XUSB Turntable',
                        'description' => "**Audio-Technica AT-LP120XUSB Direct-Drive Turntable**\n- USB output to connect to computers\n- Fully manual operation with variable speed controls\n- Comes with HS6 headshell and VM95E cartridge\n- Convert records to digital files\n- Perfect for vinyl enthusiasts\n- Mint condition. Reach out for more details."
                    ]
                ],

            ],

            "Furniture" => [

                "Bedroom Furniture" => [
                    [
                        'title' => 'Solid Oak Queen Bed Frame',
                        'description' => "**Solid Oak Queen Bed Frame**\n- Premium quality wood\n- Includes headboard and footboard\n- Easy to assemble and disassemble\n- Fits standard queen size mattresses\n- Available for pick up. No delivery.\n- Slightly used, in great condition."
                    ],

                    [
                        'title' => 'Vintage Mahogany Nightstand',
                        'description' => "**Vintage Mahogany Nightstand**\n- Classic design with a drawer and shelf\n- Genuine mahogany wood\n- Polished finish\n- Perfect for bedside storage\n- Hand-carved details. Must see to appreciate.\n- Price negotiable for serious buyers."
                    ],

                    [
                        'title' => 'Modern Wardrobe with Sliding Doors',
                        'description' => "**Modern Wardrobe with Sliding Doors**\n- Ample storage space with shelves and hanging rod\n- Glossy white finish\n- Soft-close mechanism\n- Can assist with delivery for an extra fee\n- Dimensions: 6ft x 7ft x 2ft\n- Grab this deal before it's gone!"
                    ],

                    [
                        'title' => '5-Drawer Wooden Dresser',
                        'description' => "**5-Drawer Wooden Dresser**\n- Spacious drawers with metal runners\n- Rich espresso finish\n- Suitable for bedroom or guest room\n- Minor signs of wear, reflected in price\n- Dimensions: 4ft x 3ft x 1.5ft\n- Selling due to moving. Quick sale."
                    ]
                ],

                "Living Room Furniture" => [
                    [
                        'title' => 'Leather Recliner Sofa Set',
                        'description' => "**Leather Recliner Sofa Set**\n- Includes 3-seater, 2-seater, and single\n- Premium brown leather finish\n- Built-in reclining function\n- Barely used, in pristine condition\n- Pick-up preferred, delivery negotiable\n- Perfect for a cozy living room setup."
                    ],

                    [
                        'title' => 'Glass Coffee Table',
                        'description' => "**Glass Coffee Table**\n- Modern design with wooden legs\n- Frosted glass top\n- Dimensions: 3.5ft x 2ft\n- A sleek addition to your living room\n- No scratches or damages. Like new!\n- Price slightly negotiable."
                    ],

                    [
                        'title' => 'Wooden TV Stand with Storage',
                        'description' => "**Wooden TV Stand with Storage**\n- Dark cherry wood finish\n- Multiple shelves and drawers\n- Suitable for TVs up to 65 inches\n- Cable management system at the back\n- Slight wear on the side, not noticeable from the front."
                    ],

                    [
                        'title' => 'Fabric Accent Chair',
                        'description' => "**Fabric Accent Chair**\n- Soft padded seating\n- Elegant design with wooden legs\n- Perfect for reading or lounging\n- No stains or damages\n- Compact size, fits any corner\n- Available for immediate pick up."
                    ]
                ],

                "Dining Room Furniture" => [
                    [
                        'title' => '6-Seater Wooden Dining Set',
                        'description' => "**6-Seater Wooden Dining Set**\n- Comes with 6 cushioned chairs\n- Polished wood with a fine finish\n- Sturdy and durable construction\n- Dimensions: 6ft x 3.5ft\n- Minor wear on the table's edge, reflected in price.\n- A great addition to any dining space."
                    ],

                    [
                        'title' => 'Marble Top Bar Table',
                        'description' => "**Marble Top Bar Table**\n- Genuine marble top\n- Accompanied by 2 leather bar stools\n- Perfect for small dining areas or kitchen corners\n- Elegant and modern look\n- Stools swivel 360 degrees."
                    ],

                    [
                        'title' => 'Rustic Wine Cabinet',
                        'description' => "**Rustic Wine Cabinet**\n- Crafted from high-quality wood\n- Holds up to 20 wine bottles\n- Slide-out drawer for corkscrews and accessories\n- Perfect for wine enthusiasts.\n- Some minor wear, but adds to the rustic charm."
                    ],

                    [
                        'title' => 'Expandable Dining Table',
                        'description' => "**Expandable Dining Table**\n- Ideal for smaller dining spaces\n- Expands to seat up to 8\n- Elegant dark wood finish\n- Compact when folded, spacious when expanded\n- Chairs not included."
                    ]
                ],
                "Outdoor Furniture" => [
                    [
                        'title' => 'Patio Swing with Canopy',
                        'description' => "**Patio Swing with Canopy**\n- Seats three comfortably\n- Weather-resistant fabric\n- Sturdy metal frame\n- Adjustable canopy tilt\n- A relaxing addition to your garden or patio.\n- Self-pickup required."
                    ],

                    [
                        'title' => 'Outdoor Rattan Lounge Set',
                        'description' => "**Outdoor Rattan Lounge Set**\n- Includes sofa, 2 chairs, and coffee table\n- Durable synthetic rattan material\n- Comes with plush cushions\n- Weatherproof and UV resistant\n- Perfect for poolside or garden."
                    ],

                    [
                        'title' => 'All-Weather Garden Bench',
                        'description' => "**All-Weather Garden Bench**\n- Made from durable resin material\n- Seats two comfortably\n- Rust-proof and water-resistant\n- Classic wooden design, but with longevity of resin\n- Ideal for gardens, patios, and balconies."
                    ],

                    [
                        'title' => 'Outdoor Fire Pit Table',
                        'description' => "**Outdoor Fire Pit Table**\n- Made from high-quality steel\n- Elegant stone finish\n- Propane-powered for controlled flames\n- Centerpiece for evening outdoor gatherings\n- Safety guard and cover included."
                    ]
                ],

                "Office Furniture" => [
                    [
                        'title' => 'Ergonomic Office Chair',
                        'description' => "**Ergonomic Office Chair**\n- Adjustable height and recline\n- Mesh back for breathability\n- Rolling wheels at the base\n- Provides excellent lumbar support\n- Ideal for long working hours."
                    ],

                    [
                        'title' => 'L-Shaped Office Desk',
                        'description' => "**L-Shaped Office Desk**\n- Spacious design with storage drawers\n- Dark wood finish\n- Suitable for computer setups\n- Dimensions: 5ft x 4ft\n- Compact design perfect for home offices."
                    ],

                    [
                        'title' => 'Wall-mounted Bookshelf',
                        'description' => "**Wall-mounted Bookshelf**\n- Space-saving design\n- Durable wooden construction\n- Modern white finish\n- Holds up to 30kgs of books or equipment\n- Ideal for office storage or decoration."
                    ],

                    [
                        'title' => 'Height-Adjustable Standing Desk',
                        'description' => "**Height-Adjustable Standing Desk**\n- Transitions between sitting and standing heights\n- Spacious workspace area\n- Digital control panel\n- Improve posture and productivity\n- Minor wear on the side but in perfect working condition."
                    ]
                ]

            ],
            "Fashion" => [

                "Men's Clothing" => [
                    [
                        'title' => 'Men’s Slim Fit Jeans',
                        'description' => "**Men’s Slim Fit Jeans**\n- Brand: Levi's\n- Size: 32W 34L\n- Dark blue shade\n- Lightly used, no wear or tears\n- Comfortable and stylish fit."
                    ],

                    [
                        'title' => "Men's Casual Cotton Shirt",
                        'description' => "**Men's Casual Cotton Shirt**\n- Brand: Tommy Hilfiger\n- Size: Medium\n- Checkered design\n- Ideal for summer and spring seasons\n- In excellent condition."
                    ],

                    [
                        'title' => "Men's Leather Jacket",
                        'description' => "**Men's Leather Jacket**\n- 100% genuine leather\n- Size: Large\n- Black color\n- Perfect for cool weather\n- Slight wear on the cuffs."
                    ],

                    [
                        'title' => 'Tuxedo Suit Set',
                        'description' => "**Tuxedo Suit Set**\n- Includes jacket, trousers, and tie\n- Size: 40 Regular\n- Ideal for formal occasions\n- Dry cleaned and ready to wear\n- Brand: Ralph Lauren."
                    ]
                ],

                "Women's Clothing" => [
                    [
                        'title' => 'Women’s Summer Dress',
                        'description' => "**Women’s Summer Dress**\n- Brand: Zara\n- Size: Medium\n- Floral design\n- Light and comfortable for hot weather\n- In perfect condition."
                    ],

                    [
                        'title' => 'Elegant Evening Gown',
                        'description' => "**Elegant Evening Gown**\n- Brand: Vera Wang\n- Size: Small\n- Dark blue with sequins\n- Ideal for formal events and occasions\n- Only worn once for a wedding."
                    ],

                    [
                        'title' => 'Casual Denim Jacket',
                        'description' => "**Casual Denim Jacket**\n- Perfect for layering\n- Size: Large\n- Light blue vintage style\n- Comfortable fit, ideal for all seasons\n- Some minor wear but adds character."
                    ],

                    [
                        'title' => 'Yoga Pants',
                        'description' => "**Yoga Pants**\n- Brand: Lululemon\n- Size: Medium\n- Black, stretchy material\n- Perfect for yoga and fitness activities\n- Worn a few times, still in great condition."
                    ]
                ],

                "Kids' Clothing" => [
                    [
                        'title' => 'Kids Winter Jacket',
                        'description' => "**Kids Winter Jacket**\n- Suitable for ages 5-6\n- Brand: Gap Kids\n- Waterproof and insulated\n- Bright red color\n- In excellent condition, hardly used."
                    ],

                    [
                        'title' => "Girl's Fairy Costume",
                        'description' => "**Girl's Fairy Costume**\n- Suitable for ages 4-5\n- Pink and glittery\n- Comes with wand and headband\n- Perfect for dress-up or parties\n- Worn once for a birthday party."
                    ],

                    [
                        'title' => 'Boys School Uniform Set',
                        'description' => "**Boys School Uniform Set**\n- Suitable for ages 7-8\n- Includes shirt, trousers, and tie\n- Brand: Next\n- Used for one term, still in good condition."
                    ],

                    [
                        'title' => 'Toddler Shoes',
                        'description' => "**Toddler Shoes**\n- Suitable for ages 1-2\n- Brand: Clarks\n- Cute animal design\n- Soft sole, perfect for first walkers\n- Some wear but still functional."
                    ]
                ],

                "Footwear" => [
                    [
                        'title' => 'Running Sneakers',
                        'description' => "**Running Sneakers**\n- Brand: Nike\n- Size: 10 Men's\n- Light and comfortable\n- Air cushioning for impact protection\n- Lightly used, still in good condition."
                    ],

                    [
                        'title' => 'Elegant Stilettos',
                        'description' => "**Elegant Stilettos**\n- Brand: Jimmy Choo\n- Size: 7 Women's\n- Shiny black with silver highlights\n- Worn once for an event\n- In original box with dust bag."
                    ],

                    [
                        'title' => 'Men’s Formal Shoes',
                        'description' => "**Men’s Formal Shoes**\n- Brand: Clarks\n- Size: 11 Men's\n- Genuine leather, black\n- Perfect for formal events\n- Some minor wear on the sole."
                    ],

                    [
                        'title' => "Women's Sandals",
                        'description' => "**Women's Sandals**\n- Brand: Birkenstock\n- Size: 8 Women's\n- Comfortable cork footbed\n- Ideal for summer\n- Worn a few times, still in great condition."
                    ]
                ],

                "Accessories" => [
                    [
                        'title' => 'Leather Wallet',
                        'description' => "**Leather Wallet**\n- Brand: Gucci\n- Genuine leather with embossed logo\n- Multiple card slots and coin pocket\n- Lightly used, in original box\n- Classic and timeless design."
                    ],

                    [
                        'title' => 'Gold Pendant Necklace',
                        'description' => "**Gold Pendant Necklace**\n- Brand: Tiffany & Co.\n- 18k yellow gold\n- Elegant heart-shaped pendant\n- Perfect for gifting or special occasions\n- Comes in original pouch and box."
                    ],

                    [
                        'title' => 'Sunglasses',
                        'description' => "**Sunglasses**\n- Brand: Ray-Ban\n- Classic aviator style\n- Polarized lenses\n- Protects against UVA/UVB rays\n- In original case with cleaning cloth."
                    ],

                    [
                        'title' => 'Silk Scarf',
                        'description' => "**Silk Scarf**\n- Brand: Hermès\n- 100% silk, hand-rolled edges\n- Floral design\n- Can be worn in various ways\n- Dry cleaned and ready to wear."
                    ]
                ],

            ],
            "Books, Sports & Hobbies" => [

                'Fiction Books' => [
                    [
                        'title' => 'Epic Fantasy Novel - "The Dragon\'s Keep"',
                        'description' => "**Author: A. J. Riddle**\n- 2019 Publication\n- Hardcover\n- Engaging story of knights, dragons, and magic.\n- Mint condition, no dog ears or markings.\n- A must-read for fantasy lovers!"
                    ],
                    [
                        'title' => 'Sci-Fi Thriller - "Beyond the Stars"',
                        'description' => "**Author: L.M. Drake**\n- 2021 Publication\n- Paperback\n- A thrilling space odyssey with a twist.\n- Almost new, perfect for a weekend read.\n- For those who love the unknown!"
                    ],
                    [
                        'title' => 'Historical Romance - "Whispers of the Past"',
                        'description' => "**Author: Emily Rose**\n- 2018 Publication\n- Paperback\n- A tale of love during the Civil War era.\n- Gently used with minimal wear.\n- A tearjerker for romance fans!"
                    ],
                    [
                        'title' => 'Mystery - "Shadows in the Alley"',
                        'description' => "**Author: C.J. Thorn**\n- 2020 Publication\n- Hardcover\n- A detective's pursuit of a relentless killer.\n- In excellent condition.\n- A page-turner till the end!"
                    ]
                ],

                'Non-fiction Books' => [
                    [
                        'title' => 'Biography - "Steve Jobs by Walter Isaacson"',
                        'description' => "**Author: Walter Isaacson**\n- 2011 Publication\n- Paperback\n- Comprehensive biography of Apple's co-founder.\n- In pristine condition.\n- A deep dive into the life of a tech visionary."
                    ],
                    [
                        'title' => 'Self-Help - "The Art of Mindfulness"',
                        'description' => "**Author: Dr. A.M. Phillips**\n- 2019 Publication\n- Paperback\n- A guide to living in the present moment.\n- Like new, no markings.\n- Perfect for those seeking inner peace."
                    ],
                    [
                        'title' => 'History - "The Rise and Fall of Civilizations"',
                        'description' => "**Author: Prof. L.R. Grey**\n- 2015 Publication\n- Hardcover\n- A detailed account of the world's greatest empires.\n- Slightly used with some underlining.\n- An enlightening read for history buffs!"
                    ],
                    [
                        'title' => 'Science - "The Wonders of Quantum Physics"',
                        'description' => "**Author: Dr. N.K. Quantum**\n- 2018 Publication\n- Hardcover\n- Breaking down the complexities of quantum theory.\n- In very good condition.\n- Ideal for those curious about the subatomic world."
                    ]
                ],

                'Educational Books' => [
                    [
                        'title' => 'Mathematics Grade 12 Textbook',
                        'description' => "Published in 2022\n- By Pearson Education\n- Covers calculus, statistics, and algebra.\n- Barely used, no markings or highlights.\n- Great for exam preparations!"
                    ],
                    [
                        'title' => 'Biology - Exploring Life (11th Grade)',
                        'description' => "Published in 2020\n- By McGraw-Hill\n- Covers genetics, evolution, ecology, and more.\n- In decent condition with some notes.\n- Perfect companion for biology students."
                    ],
                    [
                        'title' => 'World Literature- An Anthology',
                        'description' => "Published in 2019\n- By Riverside Press\n- Collection of classic literary works.\n- Gently used, some annotations.\n- Essential for literature students!"
                    ],
                    [
                        'title' => 'Chemistry Concepts & Applications',
                        'description' => "Published in 2021\n- By Thompson Learning\n- Detailed explanations with diagrams.\n- Almost new, very minimal use.\n- A comprehensive guide for aspiring chemists!"
                    ]
                ],

                'Sports Equipment' => [
                    [
                        'title' => 'Professional Tennis Racket',
                        'description' => "**Brand: AceMaster**\n- 2022 Model\n- Lightweight, carbon-fiber build\n- Used for only a month\n- Perfect for intermediate and professional players."
                    ],
                    [
                        'title' => 'Mountain Bike - TrailBlazer Pro',
                        'description' => "**Brand: MountainKing**\n- 27-speed gear system\n- Disk brakes, shock-absorbent frame\n- Ridden less than 50 miles\n- Ideal for adventurous terrains!"
                    ],
                    [
                        'title' => 'Golf Set with Bag',
                        'description' => "**Brand: GolfMaster**\n- Set of 14 clubs\n- Leather golf bag included\n- Clubs in mint condition, bag shows slight wear\n- Great set for golf enthusiasts!"
                    ],
                    [
                        'title' => 'Pro Football Cleats',
                        'description' => "**Brand: SprintStar**\n- Size: 10\n- Optimized for speed and grip\n- Worn only twice\n- Excellent for competitive matches."
                    ]
                ],

                'Music Instruments' => [
                    [
                        'title' => 'Acoustic Guitar - WoodFinish',
                        'description' => "**Brand: MelodyMaker**\n- Solid spruce top\n- Mahogany back and sides\n- Includes a soft-case\n- Perfect for beginners and hobbyists."
                    ],
                    [
                        'title' => 'Digital Piano with Stand',
                        'description' => "**Brand: KeyMaster**\n- 88 weighted keys\n- Multiple instrument sounds\n- Barely used, includes a bench\n- Ideal for both practice and performances."
                    ],
                    [
                        'title' => '5-Piece Drum Set',
                        'description' => "**Brand: BeatCraft**\n- Complete with cymbals and stool\n- Minor wear on snare drum head\n- Excellent for budding drummers."
                    ],
                    [
                        'title' => 'Flute - Silver Series',
                        'description' => "**Brand: WindCraft**\n- Silver-plated, C-footjoint\n- Well-maintained with a clear tone\n- Comes with cleaning kit\n- Ideal for intermediate players."
                    ]
                ],

                'Art & Collectibles' => [
                    [
                        'title' => 'Vintage Painting - Seaside Sunset',
                        'description' => "Artist: J.H. Turner\n- Oil on canvas\n- Framed, 24x18 inches\n- Serene depiction of a coastal sunset\n- Adds a classic touch to any room."
                    ],
                    [
                        'title' => 'Stamp Collection from 1920s',
                        'description' => "Over 100 vintage stamps\n- From various countries\n- Well-preserved in an album\n- A treasure for stamp collectors."
                    ],
                    [
                        'title' => 'Handcrafted Wooden Chess Set',
                        'description' => "Hand-carved pieces\n- Board made of rosewood\n- Foldable, 18x18 inches\n- Perfect for both casual games and display."
                    ],
                    [
                        'title' => 'Antique Bronze Vase',
                        'description' => "Circa: 1890\n- Intricate designs and patterns\n- Height: 15 inches\n- A beautiful addition to any art collection."
                    ]
                ],

                'Fitness & Gym Equipment' => [
                    [
                        'title' => 'Treadmill - CardioMax 500',
                        'description' => "**Brand: FitLife**\n- 20 pre-set workouts\n- Speed up to 16 km/h\n- Used for 6 months\n- Perfect for home workouts."
                    ],
                    [
                        'title' => 'Yoga Mat and Blocks Set',
                        'description' => "**Brand: ZenLife**\n- High-density foam mat\n- Includes 2 yoga blocks\n- Eco-friendly material\n- Ideal for yoga and pilates enthusiasts."
                    ],
                    [
                        'title' => 'Dumbbell Set - 5 to 50 lbs',
                        'description' => "Rubber-coated weights\n- Includes storage rack\n- Variety of weights for full-body workout\n- Essential for strength training."
                    ],
                    [
                        'title' => 'Elliptical Trainer - CrossFit Pro',
                        'description' => "**Brand: GymTech**\n- Multiple resistance levels\n- Heart rate monitor included\n- Used for 3 months\n- Great for low-impact cardio training."
                    ]
                ],

            ],

            "Real Estate" => [
                'Residential for Sale' => [
                    [
                        'title' => 'Modern 3BHK Apartment in Downtown',
                        'description' => "**Type**: 3BHK Apartment\n- **Area**: 1600 sq.ft\n- **Location**: Downtown City Center\n- 9th Floor with city view\n- Amenities: Gym, Pool, 24/7 Security\n- Ready to move in!"
                    ],
                    [
                        'title' => 'Luxury Villa with Private Pool',
                        'description' => "**Type**: 4BHK Villa\n- **Area**: 3500 sq.ft\n- **Location**: Palm Beach Road\n- Private swimming pool & garden\n- 3 Car Garage\n- Prime property for luxurious living."
                    ],
                    [
                        'title' => '2BHK Starter Home in Gated Community',
                        'description' => "**Type**: 2BHK House\n- **Area**: 1200 sq.ft\n- **Location**: Rosewood Estates\n- Community Amenities: Park, Clubhouse\n- Perfect for small families!"
                    ],
                    [
                        'title' => 'Penthouse with Panoramic City Views',
                        'description' => "**Type**: 4BHK Penthouse\n- **Area**: 2800 sq.ft\n- **Location**: Skyline Towers, Central City\n- Private Elevator, Rooftop Deck\n- An epitome of luxury and sophistication."
                    ]
                ],

                'Residential for Rent' => [
                    [
                        'title' => '1BHK Apartment near University',
                        'description' => "**Type**: 1BHK Apartment\n- **Area**: 700 sq.ft\n- **Location**: Green Park, near City University\n- 5th Floor, Furnished\n- Ideal for students and working professionals."
                    ],
                    [
                        'title' => '3BHK Fully Furnished Townhouse',
                        'description' => "**Type**: 3BHK Townhouse\n- **Area**: 1800 sq.ft\n- **Location**: Brookside Villas\n- Garden, Kids Play Area\n- Available for immediate occupancy."
                    ],
                    [
                        'title' => 'Studio Apartment in Business District',
                        'description' => "**Type**: Studio Apartment\n- **Area**: 500 sq.ft\n- **Location**: Metro Heights, Business District\n- 12th Floor, Cityscape View\n- Perfect for business travelers or singles."
                    ],
                    [
                        'title' => '2BHK Villa with Private Garden',
                        'description' => "**Type**: 2BHK Villa\n- **Area**: 1400 sq.ft\n- **Location**: Woodland Residency\n- Private Garden, Pet-friendly\n- Available from next month."
                    ]
                ],

                'Commercial for Sale' => [
                    [
                        'title' => 'Retail Shop in Busy Market',
                        'description' => "**Type**: Retail Shop\n- **Area**: 400 sq.ft\n- **Location**: Sunrise Market\n- Ground Floor, High Footfall\n- Ideal for any retail business."
                    ],
                    [
                        'title' => 'Modern Office Space in Business Hub',
                        'description' => "**Type**: Office Space\n- **Area**: 2500 sq.ft\n- **Location**: Platinum Business Park\n- 7th Floor, Pantry & Meeting Rooms\n- Prime location for corporate offices."
                    ],
                    [
                        'title' => 'Warehouse near Port',
                        'description' => "**Type**: Warehouse\n- **Area**: 10,000 sq.ft\n- **Location**: Near Westside Port\n- Easy Access, Ample Storage\n- Perfect for import/export businesses."
                    ],
                    [
                        'title' => 'Restaurant Space in City Center',
                        'description' => "**Type**: Commercial Space\n- **Area**: 3500 sq.ft\n- **Location**: Central Plaza, City Center\n- Previously operated as a restaurant\n- Highly suitable for food businesses."
                    ]
                ],

                'Commercial for Rent' => [
                    [
                        'title' => 'Shared Office Space in Tech Park',
                        'description' => "**Type**: Shared Office\n- **Area**: 800 sq.ft\n- **Location**: Silicon Tech Park\n- 5 Desks, Conference Room Access\n- Ideal for startups and freelancers."
                    ],
                    [
                        'title' => 'Shop in Mall with High Footfall',
                        'description' => "**Type**: Retail Shop\n- **Area**: 600 sq.ft\n- **Location**: Grand Mall\n- Ground Floor, near Main Entrance\n- Perfect for brands looking for visibility."
                    ],
                    [
                        'title' => 'Warehouse for Lease',
                        'description' => "**Type**: Warehouse\n- **Area**: 7000 sq.ft\n- **Location**: East Industrial Zone\n- Well-maintained, Secure premises\n- Suitable for businesses needing storage."
                    ],
                    [
                        'title' => 'Ground Floor Showroom in Prime Area',
                        'description' => "**Type**: Showroom\n- **Area**: 1500 sq.ft\n- **Location**: Diamond Avenue\n- High visibility, Ample Parking\n- Ideal for brands or agencies."
                    ]
                ],

                'Land & Plots' => [
                    [
                        'title' => 'Residential Plot in Gated Community',
                        'description' => "**Type**: Residential Plot\n- **Area**: 2400 sq.ft\n- **Location**: Green Meadows\n- Gated Community, 24/7 Security\n- Perfect for building your dream home."
                    ],
                    [
                        'title' => 'Commercial Land near Highway',
                        'description' => "**Type**: Commercial Land\n- **Area**: 5000 sq.ft\n- **Location**: Near NH45 Highway\n- High visibility, Multiple access points\n- Ideal for businesses or warehouses."
                    ],
                    [
                        'title' => 'Farm Land with Natural Pond',
                        'description' => "**Type**: Farm Land\n- **Area**: 5 acres\n- **Location**: Countryside Area\n- Natural pond, Fertile soil\n- Perfect for organic farming or resorts."
                    ],
                    [
                        'title' => 'Hillside Plot with Stunning Views',
                        'description' => "**Type**: Residential Plot\n- **Area**: 3000 sq.ft\n- **Location**: Hillview Residency\n- Overlooking the valley, Peaceful surroundings\n- A prime spot for a vacation home."
                    ]
                ],
            ],

            'Jobs' => [

                'Full-time Jobs' => [
                    [
                        'title' => 'Software Developer at TechCorp',
                        'description' => "**Position**: Software Developer\n- **Company**: TechCorp Innovations\n- **Location**: City Center, Tech Park\n- Experience: 2-5 years in Java, Spring Boot\n- Perks: Health Insurance, 401K matching"
                    ],
                    [
                        'title' => 'Marketing Manager at BrandCo',
                        'description' => "**Position**: Marketing Manager\n- **Company**: BrandCo Solutions\n- **Location**: Downtown, City Plaza\n- Experience: 5-8 years in Branding & Digital Marketing\n- Benefits: Bonus, Travel Opportunities"
                    ],
                    [
                        'title' => 'Customer Support Specialist at HelpHub',
                        'description' => "**Position**: Customer Support Specialist\n- **Company**: HelpHub Services\n- **Location**: Remote\n- Experience: 1-3 years in Customer Relations\n- Perks: Flexible Timing, Remote Work"
                    ],
                    [
                        'title' => 'Project Manager at BuildIt',
                        'description' => "**Position**: Project Manager\n- **Company**: BuildIt Constructions\n- **Location**: Westside Office Complex\n- Experience: 7-10 years in Construction Management\n- Benefits: Company Car, Health Checks"
                    ]
                ],

                'Part-time Jobs' => [
                    [
                        'title' => 'Weekend Barista at CoffeeNest',
                        'description' => "**Position**: Barista\n- **Company**: CoffeeNest Café\n- **Location**: Main Street\n- Experience: 6 months-2 years in Beverage Service\n- Schedule: Weekends only, Tips included"
                    ],
                    [
                        'title' => 'Evening Tutor for Mathematics',
                        'description' => "**Position**: Math Tutor\n- **Institution**: City Learning Center\n- **Location**: North District\n- Experience: Degree in Mathematics or Education\n- Hours: 6pm to 9pm, Mon to Fri"
                    ],
                    [
                        'title' => 'Library Assistant at UniLibrary',
                        'description' => "**Position**: Library Assistant\n- **Institution**: City University\n- **Location**: Campus Library\n- Experience: Familiarity with Library Management System\n- Hours: 12pm to 5pm, Weekdays"
                    ],
                    [
                        'title' => 'Gym Instructor at FitLife',
                        'description' => "**Position**: Gym Instructor\n- **Company**: FitLife Gym\n- **Location**: Downtown\n- Certification in Fitness Training\n- Shifts: Morning or Evening, 4 hours each"
                    ]
                ],

                'Internships' => [
                    [
                        'title' => 'Graphic Design Intern at CreativeWorks',
                        'description' => "**Position**: Design Intern\n- **Company**: CreativeWorks Studio\n- **Location**: Eastside Tech Park\n- Skills: Adobe Suite, Creative Portfolio\n- Stipend: $500/month, 3 months duration"
                    ],
                    [
                        'title' => 'Business Analyst Intern at CorpAnalytica',
                        'description' => "**Position**: BA Intern\n- **Company**: CorpAnalytica\n- **Location**: Downtown Office\n- Skills: Data Analysis, Excel\n- Stipend: $600/month, 6 months duration"
                    ],
                    [
                        'title' => 'Journalism Intern at DailyNews',
                        'description' => "**Position**: Journalism Intern\n- **Publication**: DailyNews Paper\n- **Location**: City Center\n- Skills: Writing, News Gathering\n- Stipend: $400/month, 4 months duration"
                    ],
                    [
                        'title' => 'HR Intern at PeopleFirst',
                        'description' => "**Position**: HR Intern\n- **Company**: PeopleFirst HR Solutions\n- **Location**: Corporate Park\n- Skills: Communication, Basic HR knowledge\n- Stipend: $550/month, 5 months duration"
                    ]
                ],

                'Freelance Jobs' => [
                    [
                        'title' => 'Freelance Content Writer for WebTech',
                        'description' => "**Position**: Content Writer\n- **Company**: WebTech Solutions\n- **Location**: Remote\n- Skills: SEO writing, Blogging\n- Pay: $50 per article, Long-term collaboration"
                    ],
                    [
                        'title' => 'Freelance Web Developer for CodeCraft',
                        'description' => "**Position**: Web Developer\n- **Company**: CodeCraft Creations\n- **Location**: Remote\n- Skills: HTML, CSS, JavaScript, WordPress\n- Pay: $500 per project, Flexible timings"
                    ],
                    [
                        'title' => 'Freelance Photographer for SnapStudio',
                        'description' => "**Position**: Photographer\n- **Company**: SnapStudio\n- **Location**: Various Locations\n- Skills: Portrait & Landscape Photography\n- Pay: $150 per session, Equipment provided"
                    ],
                    [
                        'title' => 'Freelance Translator (Spanish-English)',
                        'description' => "**Position**: Translator\n- **Institution**: Global Translations\n- **Location**: Remote\n- Skills: Fluent in Spanish & English\n- Pay: $30 per page, Ongoing need"
                    ]
                ],

                'Government Jobs' => [
                    [
                        'title' => 'Administrative Officer at City Hall',
                        'description' => "**Position**: Admin Officer\n- **Department**: City Administration\n- **Location**: City Hall\n- Experience: 3-5 years in Public Administration\n- Benefits: Pension, Health Care, Paid Leave"
                    ],
                    [
                        'title' => 'Research Scientist at National Lab',
                        'description' => "**Position**: Research Scientist\n- **Institution**: National Research Lab\n- **Location**: Science Park\n- Qualification: PhD in Biology\n- Perks: Research Grants, Collaboration Opportunities"
                    ],
                    [
                        'title' => 'Forest Ranger at National Park',
                        'description' => "**Position**: Forest Ranger\n- **Department**: Wildlife & Conservation\n- **Location**: Redwood National Park\n- Skills: Wildlife Management, First Aid\n- Benefits: Govt. Accommodation, Insurance"
                    ],
                    [
                        'title' => 'School Teacher at Public School',
                        'description' => "**Position**: Math Teacher\n- **Institution**: Downtown Public School\n- **Location**: City Center\n- Qualification: Bachelor's in Education\n- Perks: Summer Vacations, Retirement Benefits"
                    ]
                ],
            ],

            'Service' => [

                'Home Services' => [
                    [
                        'title' => 'Professional Home Cleaning Services',
                        'description' => "**Service**: Complete Home Cleaning\n- **Duration**: 4-6 hours\n- **Coverage**: All rooms, kitchen, bathroom\n- Tools and detergents provided\n- **Pricing**: $100, Discounts on bi-weekly cleaning"
                    ],
                    [
                        'title' => 'Expert Plumbing Solutions',
                        'description' => "**Service**: Plumbing Repairs & Installation\n- **Availability**: 24/7 emergency service\n- Specialize in leaks, blockages, installations\n- **Pricing**: Starts from $50, Free Quote available"
                    ],
                    [
                        'title' => 'Electrical Repairs by Certified Electricians',
                        'description' => "**Service**: Electrical Repairs\n- **Coverage**: Wiring, Fuse boxes, Appliance installations\n- Certified & Insured Electricians\n- **Pricing**: Starts from $70, On-spot assessment"
                    ],
                    [
                        'title' => 'Landscaping & Gardening Services',
                        'description' => "**Service**: Gardening & Landscaping\n- **Features**: Lawn mowing, Planting, Garden Design\n- Professional tools, Organic materials\n- **Pricing**: Custom quote based on garden size"
                    ]
                ],

                'Automotive Services' => [
                    [
                        'title' => 'Full Car Service & Oil Change',
                        'description' => "**Service**: Complete Car Checkup\n- **Coverage**: Oil change, Brake check, Engine tuning\n- **Location**: Downtown Garage\n- **Pricing**: $150, Free re-check within a month"
                    ],
                    [
                        'title' => 'Professional Car Detailing',
                        'description' => "**Service**: Interior & Exterior Detailing\n- High-quality products used\n- Scratch removal, Polish & Wax\n- **Pricing**: Starts from $100, Premium packages available"
                    ],
                    [
                        'title' => 'Tire Rotation & Balancing',
                        'description' => "**Service**: Tire Maintenance\n- **Features**: Rotation, Balancing, Pressure check\n- **Location**: AutoHub, Main Street\n- **Pricing**: $50 for complete service"
                    ],
                    [
                        'title' => '24/7 Towing Services',
                        'description' => "**Service**: Vehicle Towing\n- Immediate response, Safe transportation\n- Coverage: Entire city & outskirts\n- **Pricing**: Starts from $80, No hidden charges"
                    ]
                ],

                'Health & Wellness Services' => [
                    [
                        'title' => 'Personal Yoga Instructor',
                        'description' => "**Service**: Personal Yoga Training\n- Sessions: Morning & Evening batches\n- **Location**: Central Park or Home service\n- **Pricing**: $30 per session, Packages available"
                    ],
                    [
                        'title' => 'Certified Dietitian Consultation',
                        'description' => "**Service**: Nutrition & Diet Planning\n- Personalized diet charts, Online consultation available\n- **Pricing**: $40 per consultation, Monthly plans available"
                    ],
                    [
                        'title' => 'Therapeutic Massage Services',
                        'description' => "**Service**: Full Body Massage\n- Trained therapists, Organic oils\n- **Location**: Wellness Spa, Downtown\n- **Pricing**: Starts from $70, Various therapies available"
                    ],
                    [
                        'title' => 'Gym Membership at FitLife',
                        'description' => "**Service**: Gym & Fitness\n- State-of-the-art equipment, Personal trainers\n- **Location**: FitLife Gym, North Street\n- **Pricing**: $50/month, Discounts on yearly subscription"
                    ]
                ],

                'Event Services' => [
                    [
                        'title' => 'Wedding Photography by SnapMoments',
                        'description' => "**Service**: Wedding Photography & Videography\n- Coverage: Pre-wedding, Ceremony, Reception\n- **Features**: Drone shots, Photo album\n- **Pricing**: Custom packages, Starts from $1000"
                    ],
                    [
                        'title' => 'DJ Services for Parties & Events',
                        'description' => "**Service**: DJ & Music\n- Genres: Pop, Rock, Electronic, Jazz\n- **Location**: Any venue\n- **Pricing**: Starts from $300, Special requests accommodated"
                    ],
                    [
                        'title' => 'Birthday & Theme Party Organizers',
                        'description' => "**Service**: Event Planning\n- Features: Decor, Catering, Entertainment\n- Custom themes: Superheroes, Fairy-tale, etc.\n- **Pricing**: Based on requirements, Free initial consultation"
                    ],
                    [
                        'title' => 'Corporate Event Management by ProEvents',
                        'description' => "**Service**: Corporate Gatherings & Seminars\n- End-to-end management\n- **Features**: Venue booking, AV setup, Catering\n- **Pricing**: Quote on request, Comprehensive packages"
                    ]
                ],

                'Learning & Education Services' => [
                    [
                        'title' => 'Piano Lessons by Maestro John',
                        'description' => "**Service**: Piano Training\n- Levels: Beginner to Advanced\n- **Location**: Music Academy or Home service\n- **Pricing**: $40 per hour, Discount on monthly enrollment"
                    ],
                    [
                        'title' => 'Math Tutoring for High School Students',
                        'description' => "**Service**: Math Tutoring\n- **Coverage**: Algebra, Geometry, Calculus\n- **Location**: City Learning Center\n- **Pricing**: $30 per hour, Group sessions available"
                    ],
                    [
                        'title' => 'Language Learning - Spanish Classes',
                        'description' => "**Service**: Spanish Language Training\n- Levels: Basic to Fluent\n- Interactive sessions, Study material provided\n- **Pricing**: $25 per hour, Online classes option"
                    ],
                    [
                        'title' => 'Digital Marketing Course by WebGurus',
                        'description' => "**Service**: Digital Marketing Training\n- Topics: SEO, PPC, Social Media, Analytics\n- **Location**: WebGurus Institute, Downtown\n- **Pricing**: $500 for full course, Certification provided"
                    ]
                ],
                'Business Services' => [
                    [
                        'title' => 'Professional Logo Design Services',
                        'description' => "**Service**: Logo & Branding Design\n- **Features**: Custom designs, Unlimited revisions\n- **Platform**: Print & Digital compatible\n- **Pricing**: Starts from $100, Package deals available"
                    ],
                    [
                        'title' => 'Expert SEO Consultation & Services',
                        'description' => "**Service**: SEO & Online Presence\n- **Features**: Keyword research, On-page & Off-page SEO\n- **Benefits**: Increased traffic & rankings\n- **Pricing**: Monthly packages starting $300"
                    ],
                    [
                        'title' => 'Content Writing & Blogging Services',
                        'description' => "**Service**: Content Creation\n- **Type**: Blogs, Articles, Product Descriptions\n- **Benefits**: Engaging content, Improved SEO\n- **Pricing**: Starts from $0.05 per word, Bulk discounts available"
                    ],
                    [
                        'title' => 'Full-Suite Digital Marketing Solutions',
                        'description' => "**Service**: Comprehensive Marketing\n- **Coverage**: SEO, PPC, Social Media, Email Marketing\n- Dedicated account manager\n- **Pricing**: Custom quotes based on business needs"
                    ]
                ]
            ],

            'Pets' => [
                'Dogs & Puppies' => [
                    [
                        'title' => 'Adorable Golden Retriever Puppies',
                        'description' => "**Breed**: Golden Retriever\n- **Age**: 8 weeks\n- **Vaccinated**: Yes\n- **Price**: $800 per puppy\n- Contact for more details and viewing appointments."
                    ],
                    [
                        'title' => 'German Shepherd Pups for Sale',
                        'description' => "**Breed**: German Shepherd\n- **Age**: 10 weeks\n- **Health**: Dewormed, Vaccinated\n- **Price**: $750\n- Pure breed with certification. Limited pups available!"
                    ],
                    [
                        'title' => 'Cute Beagle Pups Looking for a Home',
                        'description' => "**Breed**: Beagle\n- **Age**: 7 weeks\n- **Health**: Dewormed, Vaccinated\n- **Price**: $600\n- Lively and playful. Ideal for families with kids."
                    ],
                    [
                        'title' => 'Rottweiler Puppies with Papers',
                        'description' => "**Breed**: Rottweiler\n- **Age**: 9 weeks\n- **Health**: Dewormed, Vaccinated\n- **Price**: $850\n- Strong and protective breed. Training guide included."
                    ],
                ],
                'Cats & Kittens' => [
                    [
                        'title' => 'Adorable Siamese Kitten Pair',
                        'description' => "**Breed**: Siamese\n- **Age**: 8 weeks\n- **Vaccinated**: Yes\n- **Price**: $300 for both\n- Playful, cuddly, and well-socialized with family and pets. Both are litter-trained."
                    ],
                    [
                        'title' => 'Purebred Siamese Kittens Available',
                        'description' => "**Breed**: Siamese\n- **Age**: 12 weeks\n- **Health**: Vet checked, Vaccinated\n- **Price**: $400\n- Beautiful markings. Playful and friendly nature."
                    ],
                    [
                        'title' => 'Persian Kittens with Blue Eyes',
                        'description' => "**Breed**: Persian\n- **Age**: 11 weeks\n- **Color**: White with blue eyes\n- **Price**: $500\n- Affectionate and playful. Good with other pets."
                    ],
                    [
                        'title' => 'Exotic Shorthair Kittens',
                        'description' => "**Breed**: Exotic Shorthair\n- **Age**: 10 weeks\n- **Color**: Multi-colored\n- **Price**: $450\n- Low-maintenance and affectionate. Ideal for apartments."
                    ],
                ],

                'Birds' => [
                    [
                        'title' => 'Colorful Lovebirds Pair',
                        'description' => "**Species**: Lovebird\n- **Age**: 5 months\n- **Specialty**: Bonded pair\n- **Price**: $80 for both\n- Playful and vibrant. Cage and toys included."
                    ],
                    [
                        'title' => 'Rare African Grey Parrot',
                        'description' => "**Species**: African Grey\n- **Age**: 2 years\n- **Specialty**: Mimics sounds and voices\n- **Price**: $1000\n- Tamed and very intelligent. Comes with large cage."
                    ],
                    [
                        'title' => 'Hand Raised Cockatiels',
                        'description' => "**Species**: Cockatiel\n- **Age**: 3 months\n- **Specialty**: Singing melodies\n- **Price**: $70 each\n- Gentle and affectionate. Ideal for families."
                    ],
                    [
                        'title' => 'Macaw Chicks - Blue & Gold',
                        'description' => "**Species**: Blue & Gold Macaw\n- **Age**: 10 weeks\n- **Specialty**: Bright colors\n- **Price**: $1200\n- Rare and gorgeous. Hand-raised with love."
                    ],
                ],

                'Fish & Aquariums' => [
                    [
                        'title' => 'Freshwater Aquarium Starter Kit',
                        'description' => "**Type**: Freshwater\n- **Includes**: Guppies, Plants, and Filter\n- **Price**: $100\n- Perfect for beginners. Tank size: 10 gallons."
                    ],
                    [
                        'title' => 'Angelfish - Black and White',
                        'description' => "**Species**: Angelfish\n- **Age**: 6 months\n- **Size**: Medium\n- **Price**: $15 each\n- Graceful swimmers. Ideal for community tanks."
                    ],
                    [
                        'title' => 'Coral Pack for Marine Aquariums',
                        'description' => "**Type**: Mixed Corals\n- **Varieties**: 5 types\n- **Price**: $200\n- Brighten up your marine tank with these beautiful corals."
                    ],
                    [
                        'title' => 'Discus Fish - Assorted Colors',
                        'description' => "**Species**: Discus\n- **Age**: 8 months\n- **Size**: Large\n- **Price**: $50 each\n- King of the freshwater aquarium. Various colors available."
                    ],
                ],

                'Pet Food & Accessories' => [
                    [
                        'title' => 'Premium Dog Food - 20 lbs',
                        'description' => "**Brand**: DogDelight\n- **Type**: Dry Kibble\n- **Weight**: 20 lbs\n- **Price**: $40\n- For all breeds. Packed with nutrition."
                    ],
                    [
                        'title' => 'Cat Tree with Scratching Posts',
                        'description' => "**Type**: Cat Tree\n- **Height**: 5 ft\n- **Price**: $120\n- Multiple levels and hammocks. Keep your cat entertained!"
                    ],
                    [
                        'title' => 'Aquarium LED Lights',
                        'description' => "**Type**: Freshwater & Marine\n- **Watt**: 20W\n- **Price**: $60\n- Enhances fish and coral colors. Energy efficient."
                    ],
                    [
                        'title' => 'Bird Seed Mix - 5 lbs',
                        'description' => "**Type**: Mixed Seeds\n- **Weight**: 5 lbs\n- **Price**: $10\n- For parrots, canaries, and finches. Nutritious and fresh."
                    ],
                ],

                'Pet Services' => [
                    [
                        'title' => 'Dog Training & Behavior Sessions',
                        'description' => "**Service**: Obedience, Tricks, Behavior Modification\n- **Location**: [Your City]\n- **Pricing**: Starts from $40 per session\n- Certified trainers. Positive methods."
                    ],
                    [
                        'title' => 'Aquarium Setup & Maintenance',
                        'description' => "**Service**: Setup, Cleaning, Water Quality Checks\n- **Location**: [Your City]\n- **Pricing**: Starts from $50\n- For freshwater and marine tanks. Experienced staff."
                    ],
                    [
                        'title' => 'Cat Grooming - Home Visit',
                        'description' => "**Service**: Bath, Nail Trimming, Ear Cleaning\n- **Location**: [Your City]\n- **Pricing**: $60 per visit\n- Gentle handling. Leave your feline looking fabulous!"
                    ],
                    [
                        'title' => 'Bird Health Check & Nail Clipping',
                        'description' => "**Service**: Health Check, Nail and Beak Trimming\n- **Location**: [Your City]\n- **Pricing**: $30 per visit\n- Experienced avian vets. Keep your bird healthy and happy!"
                    ],
                ],

            ]


        ];

        $subcategoryTags = [
            'Smartphones' => ['Android', 'iOS', '5G', 'Touchscreen', 'Dual SIM'],
            'Feature Phones' => ['Basic Phone', 'Long Battery', 'Rugged', 'Compact'],
            'Mobile Accessories' => ['Chargers', 'Cases', 'Headphones', 'Screen Protectors'],
            'SIM Cards' => ['Prepaid SIM', 'Postpaid Plans', 'Data Plans', 'International Roaming'],

            'Sports Bikes' => ['Racing', 'High Performance', 'Lightweight', 'Sporty'],
            'Scooters' => ['Electric', 'Gas Scooter', 'Commuter', 'Foldable'],
            'Cruiser Bikes' => ['Long Rides', 'Comfortable', 'Classic Design', 'Touring'],
            'Bike Accessories' => ['Helmets', 'Gloves', 'Jackets', 'Bike Locks'],

            'Home Appliances' => ['Energy Efficient', 'Smart Home', 'Warranty', 'Durable'],
            'Kitchen Appliances' => ['Blenders', 'Microwaves', 'Coffee Makers', 'Toasters'],
            'Computers & Laptops' => ['Gaming', 'Business', 'Portable', 'High Performance'],
            'Cameras & Accessories' => ['DSLR', 'Mirrorless', 'Tripods', 'Lenses'],
            'TV & Video' => ['4K UHD', 'Smart TV', 'LED', 'Home Theater'],
            'Audio & Music Equipment' => ['Speakers', 'Microphones', 'Mixers', 'Headphones'],

            'Bedroom Furniture' => ['Beds', 'Wardrobes', 'Dressers', 'Nightstands'],
            'Living Room Furniture' => ['Sofas', 'Coffee Tables', 'Recliners', 'Bookshelves'],
            'Dining Room Furniture' => ['Dining Tables', 'Chairs', 'Bar Stools', 'Buffets'],
            'Outdoor Furniture' => ['Patio Sets', 'Garden Chairs', 'Umbrellas', 'Grills'],
            'Office Furniture' => ['Desks', 'Office Chairs', 'File Cabinets', 'Bookcases'],

            'Men\'s Clothing' => ['Suits', 'Casual Wear', 'Outerwear', 'Formal'],
            'Women\'s Clothing' => ['Dresses', 'Tops', 'Skirts', 'Activewear'],
            'Kids\' Clothing' => ['School Wear', 'Casual Clothes', 'Party Wear', 'Sportswear'],
            'Footwear' => ['Sneakers', 'Boots', 'Sandals', 'Formal Shoes'],
            'Accessories' => ['Watches', 'Jewelry', 'Bags', 'Belts'],

            'Fiction Books' => ['Novels', 'Science Fiction', 'Fantasy', 'Mystery'],
            'Non-fiction Books' => ['Biographies', 'History', 'Self-help', 'Travel'],
            'Educational Books' => ['Textbooks', 'Reference', 'Languages', 'Science & Math'],
            'Sports Equipment' => ['Fitness Gear', 'Outdoor Sports', 'Team Sports', 'Water Sports'],
            'Music Instruments' => ['Guitars', 'Keyboards', 'Percussions', 'Wind Instruments'],
            'Art & Collectibles' => ['Paintings', 'Sculptures', 'Antiques', 'Rare Items'],
            'Fitness & Gym Equipment' => ['Treadmills', 'Weights', 'Yoga Mats', 'Exercise Bikes'],

            'Residential for Sale' => ['Houses', 'Apartments', 'Villas', 'Townhouses'],
            'Residential for Rent' => ['Short-term Rentals', 'Long-term Rentals', 'Furnished Apartments', 'Unfurnished Apartments'],
            'Commercial for Sale' => ['Office Space', 'Retail Space', 'Warehouses', 'Industrial Properties'],
            'Commercial for Rent' => ['Co-working Spaces', 'Shops', 'Showrooms', 'Storage Units'],
            'Land & Plots' => ['Residential Land', 'Commercial Land', 'Agricultural Land', 'Undeveloped Land'],

            'Full-time Jobs' => ['Tech', 'Finance', 'Healthcare', 'Education'],
            'Part-time Jobs' => ['Retail', 'Hospitality', 'Customer Service', 'Tutoring'],
            'Internships' => ['Summer Internship', 'Co-op', 'Research', 'Virtual Internship'],
            'Freelance Jobs' => ['Writing', 'Design', 'Programming', 'Consulting'],
            'Government Jobs' => ['Public Service', 'Administration', 'Law Enforcement', 'Defense'],

            'Home Services' => ['Cleaning', 'Repair', 'Renovation', 'Landscaping'],
            'Automotive Services' => ['Maintenance', 'Repair', 'Car Wash', 'Detailing'],
            'Health & Wellness Services' => ['Massage', 'Personal Training', 'Nutrition', 'Mental Health'],
            'Event Services' => ['Catering', 'Photography', 'Planning', 'Decoration'],
            'Learning & Education Services' => ['Tutoring', 'Courses', 'Workshops', 'Online Education'],
            'Business Services' => ['Consulting', 'Marketing', 'Legal', 'Accounting'],

            'Dogs & Puppies' => ['Labrador', 'German Shepherd', 'Bulldog', 'Poodle'],
            'Cats & Kittens' => ['Persian', 'Siamese', 'Maine Coon', 'Ragdoll'],
            'Birds' => ['Parrots', 'Canaries', 'Finches', 'Pigeons'],
            'Fish & Aquariums' => ['Freshwater Fish', 'Saltwater Fish', 'Tanks', 'Decorations'],
            'Pet Food & Accessories' => ['Pet Food', 'Toys', 'Grooming', 'Beds'],
            'Pet Services' => ['Veterinary', 'Training', 'Grooming', 'Pet Sitting'],
        ];

       // Set the locale to en_US
        $faker = \Faker\Factory::create('en_US');
        $priceTypeIds = DB::table('price_types')->pluck('id')->toArray();

        // Looping through each main category
        foreach ($adsTemplates as $mainCategory => $subCategories) {
            foreach ($subCategories as $subCategory => $ads) {

                $categoryId = DB::table('categories')->where('name->en', $subCategory)->value('id');
                $tags = $subcategoryTags[$subCategory];

                if (!$categoryId) continue; // Skip if the category is not found

                $adTypeId = Category::where('name->en', $subCategory)->first()->adType?->id;

                foreach ($ads as $ad) {
                    // Fetch a random US state
                    $state = $faker->state;
                    // Fetch a random city in the selected state
                    $city = $faker->city;
                    // Set the country to United States
                    $country = "United States";
                    // Generate latitude and longitude that's roughly in the selected state
                    $latitude = $faker->latitude($min = 25, $max = 49); // Rough latitude range for the US
                    $longitude = $faker->longitude($min = -125, $max = -67); // Rough longitude range for the US

                    $newAd = Ad::create([
                        'ad_type_id' => $adTypeId,
                        'title' => $ad['title'],
                        'tags' => $tags,
                        'description' => $ad['description'],
                        'price' => $faker->numberBetween(10, 100) * 10, // Price between 10 and 1000
                        'price_type_id' => $faker->randomElement($priceTypeIds),
                        'posted_date' => $faker->dateTimeBetween('-1 month', 'now'),
                        'user_id' => $faker->randomElement($userIds),
                        'category_id' => $categoryId,
                        'for_sale_by' => $faker->randomElement(['owner', 'business']),
                        'city' => $city,
                        'state' => $state,
                        'country' => $country,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'location_name' => $city,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Update the slug with the UUID
                    $newAd->slug = Str::slug(Str::limit($ad['title'], 138)) . '-' . substr($newAd->id, 0, 8);
                    $newAd->save();
                }
            }
        }

    }
}
