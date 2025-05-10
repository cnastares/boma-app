<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\FieldTemplate;
use App\Models\FieldTemplateMappings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldTemplateSeeder extends Seeder
{

    public function getJobTemplateData()
    {
        return [
            [
                'name' => 'Job',
                'fields' => [
                    // [
                    //     'name' => 'Job Title',
                    //     'helpertext' => 'Descriptive title for the job position',
                    //     'type' => 'text',
                    // ],
                    // [
                    //     'name' => 'Job Description',
                    //     'helpertext' => 'Detailed information about the responsibilities and requirements of the job.',
                    //     'type' => 'textarea',
                    // ],
                    [
                        'name' => 'Company/Organization',
                        'helpertext' => 'Name of the hiring company or organization.',
                        'type' => 'text',
                    ],
                    [
                        'name' => 'Job Type',
                        'helpertext' => 'Full-time, part-time, contract,freelance, internship, etc.',
                        'type' => 'select',
                        'options' => ['Full Time', 'Part time', 'Contract'],
                        'filterable' => true
                    ],
                    [
                        'name' => 'Salary/Compensation/Payment',
                        'helpertext' => 'Range or specific amount offered for the position.',
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Payment Type',
                        'helpertext' => 'Type of payment option for the job position',
                        'type' => 'select',
                        'options' => ['Hourly', 'Monthly', 'Annually', 'Per Project', 'Commission'],
                        'filterable' => true
                    ],
                    [
                        'name' => 'Experience level',
                        'helpertext' => 'Entry-level, mid-level, senior, etc.',
                        'type' => 'tagsinput',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Education level',
                        'helpertext' => 'Minimum education qualifications required for the job.',
                        'type' => 'tagsinput',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Skills Required',
                        'helpertext' => 'Specific skills or qualifications necessary for the role.',
                        'type' => 'tagsinput',
                        'searchable' => true
                    ],
                    [
                        'name' => 'Job Post Date',
                        'helpertext' => 'Date on which the job position was posted',
                        'type' => 'date',
                    ],
                    [
                        'name' => 'Application Deadline',
                        'helpertext' => 'Date by which applicants must submit their applications.',
                        'type' => 'date',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Contact Information',
                        'helpertext' => 'How applicants can reach out for more information or submit their applications.',
                        'type' => 'textarea',
                    ],
                    [
                        'name' => 'Application Process',
                        'helpertext' => 'Details on how candidates should apply, including any required documents or steps.',
                        'type' => 'text',
                    ],

                    // [
                    //     'name' => 'Remote Work',
                    //     'helpertext' => 'Indication of whether the job allows remote work or is location-specific.',
                    //     'type' => 'text',
                    //     'filterable' => true
                    // ],
                    [
                        'name' => 'Job Category',
                        'helpertext' => 'Categorization of the job (e.g., IT,Sales, Marketing, Healthcare, etc.).',
                        'type' => 'tagsinput',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Benefits',
                        'helpertext' => 'Information on any additional perks or benefits associated with the job.',
                        'type' => 'textarea',
                    ],
                    [
                        'name' => 'Company Culture',
                        'helpertext' => "A brief description of the company's values and work environment.",
                        'type' => 'textarea',
                    ],
                    [
                        'name' => 'Company size',
                        'helpertext' => "A brief description of the company's size and presence.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                ]
            ],
            [
                'name' => 'Vehicle',
                'fields' => [
                    [
                        'name' => 'colour',
                        'helpertext' => "The exterior colour of the vehicle",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    // [
                    //     'name' => 'Condition',
                    //     'helpertext' => 'The condition of the vehicle',
                    //     'type' => 'select',
                    //     'options' => ['New', 'used', 'refurbished'],
                    //     'filterable' => true
                    // ],
                    [
                        'name' => 'Contact Information',
                        'helpertext' => 'How potential buyers can reach out for more details or to inquire about the PCP/HP plan.',
                        'type' => 'textarea',
                    ],
                    [
                        'name' => 'Contract Duration',
                        'helpertext' => 'The length of the PCP/HP contract, typically in months.',
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Deposit Amount',
                        'helpertext' => 'The initial upfront payment required for the PCP/ HP plan.',
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Excess Mileage Charges',
                        'helpertext' => 'Charges incurred for exceeding the agreed-upon mileage limit.',
                        'type' => 'text',
                    ],
                    [
                        'name' => 'Fuel Type',
                        'helpertext' => 'Options such as gasoline, diesel,electric, hybrid, etc.',
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'PCP/HP Provider',
                        'helpertext' => 'Name of the financial institution or provider offering the PCP/HP plan.',
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Interest Rate',
                        'helpertext' => 'The annual interest rate applied to the the PCP/HP plan.',
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Make/Manufacturer',
                        'helpertext' => 'The brand or manufacturer of the vehicle.',
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Mileage Allowance',
                        'helpertext' => 'The maximum number of miles allowed during the PCP contract.',
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Mileage',
                        'helpertext' => 'The total distance the vehicle has traveled, usually in miles or kilometers.',
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Model',
                        'helpertext' => 'The specific model or version of the vehicle.',
                        'type' => 'text',
                    ],
                    [
                        'name' => 'Monthly Payment',
                        'helpertext' => 'The recurring monthly payment for the PCP/HP plan.',
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Ownership History',
                        'helpertext' => 'Number of previous owners and any relevant history.',
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Price',
                        'helpertext' => 'The total cost of the vehicle for the Cash/PCP/HP plan.',
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Residual value',
                        'helpertext' => 'The estimated value of the vehicle at the end of the PCP contract.',
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Seller Information',
                        'helpertext' => 'Contact details or information about the seller or PCP/HP provider.',
                        'type' => 'number',
                    ],
                    [
                        'name' => 'Total Repayment Amount',
                        'helpertext' => 'The overall amount paid over the course of the PCP/HP contract,including interest.',
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Transmission Type',
                        'helpertext' => 'Automatic, manual, or other',
                        'type' => 'text',
                        'filterable' => true
                    ],
                    // [
                    //     'name' => 'Vehicle Description',
                    //     'helpertext' => 'Additional details about the vehicle, including its current condition and any modifications.',
                    //     'type' => 'text',
                    // ],
                    [
                        'name' => 'Vehicle Features',
                        'helpertext' => 'Specific features such as air conditioning, power windows, navigation system, etc.',
                        'type' => 'tagsinput',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Vehicle Identification Number (VIN)',
                        'helpertext' => 'A unique code used to identify individual motor vehicles.',
                        'type' => 'text',
                    ],
                    [
                        'name' => 'Vehicle Type',
                        'helpertext' => "Specify whether it's a car, motorcycle, truck, SUV, etc.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Year of Manufacture:',
                        'helpertext' => "The year the vehicle was manufactured.",
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Year of Purchase:',
                        'helpertext' => "The year the vehicle was purchased.",
                        'type' => 'number',
                        'filterable' => true
                    ],
                ]
            ],
            [
                'name' => 'Real Estate',
                'fields' => [
                    [
                        'name' => 'Property Type',
                        'helpertext' => "Specify whether it's residential,commercial, land, or other property types.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Listing Type',
                        'helpertext' => "For sale, for rent, or lease options.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Number of Bedrooms',
                        'helpertext' => "For residential properties, indicate the number of bedrooms.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Number of Bathrooms',
                        'helpertext' => "For residential properties, indicate the number of bathrooms.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Property Size',
                        'helpertext' => "The size of the property in square feet or square meters.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Land Size',
                        'helpertext' => "For land listings, specify the size of the land.",
                        'type' => 'text',
                    ],
                    [
                        'name' => 'Year Built',
                        'helpertext' => "The year the property was constructed.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    // [
                    //     'name' => 'Condition',
                    //     'helpertext' => "New, pre-owned, or under construction.",
                    //     'type' => 'select',
                    //     'options' => ['New', 'pre-owned', 'under construction'],
                    //     'filterable' => true
                    // ],
                    [
                        'name' => 'Price',
                        'helpertext' => "The cost of the property or rental rate.",
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Amenities',
                        'helpertext' => "Highlight specific features such as swimming pool, garage, garden, etc.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Utilities',
                        'helpertext' => "Information on available utilities like electricity, water, gas, etc.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Parking',
                        'helpertext' => "Indicate the availability of parking spaces.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    // [
                    //     'name' => 'Description',
                    //     'helpertext' => "Detailed information about the property, including its features and unique selling points.",
                    //     'type' => 'textarea',
                    //     'filterable' => true
                    // ],
                    [
                        'name' => 'Contact Information',
                        'helpertext' => "How interested parties can reach out for more details or to schedule a viewing.",
                        'type' => 'textarea',
                    ],
                    [
                        'name' => 'Seller/Broker Information',
                        'helpertext' => "Contact details or information about the property owner or listing agent.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                ]
            ],
            [
                'name' => 'Consumer (Tradesmen) Services',
                'fields' => [
                    [
                        'name' => 'Service Category',
                        'helpertext' => "Categorize services into types such as home services, beauty and wellness, event planning, etc.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Service (Trade) Type',
                        'helpertext' => "Specify the specific service being offered (e.g., plumbing, cleaning, tutoring, electrical, carpentry,).",
                        'type' => 'text',
                    ],
                    // [
                    //     'name' => 'Description',
                    //     'helpertext' => "Detailed information about the service, including what it entails.",
                    //     'type' => 'textarea',
                    // ],
                    [
                        'name' => 'Service Provider or Business Name',
                        'helpertext' => "Name or business name of the service provider.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Price',
                        'helpertext' => "The cost of the service, whether hourly, per session, or with other pricing structures.",
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Availability',
                        'helpertext' => "Days and times when the service is available.",
                        'type' => 'datetime',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Reviews and Ratings',
                        'helpertext' => "Feedback and ratings from previous customers.",
                        'type' => 'textarea',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Contact Information',
                        'helpertext' => "How potential customers can reach out to the service provider.",
                        'type' => 'textarea',
                    ],
                    [
                        'name' => 'Business Name',
                        'helpertext' => "Name of the tradesman or business providing the service.",
                        'type' => 'text',
                    ],
                    [
                        'name' => 'Services Offered',
                        'helpertext' => "Detailed list of specific services provided within the chosen trade.",
                        'type' => 'textarea',
                    ],
                    [
                        'name' => 'Certifications/Licenses',
                        'helpertext' => "Any relevant certifications or licenses held by the tradesman or business.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Experience',
                        'helpertext' => "Number of years or projects of experience in the trade.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Availability',
                        'helpertext' => "Days and times when the tradesman is available for work.",
                        'type' => 'datetime',
                    ],
                ]
            ],
            [
                'name' => 'Consumer Products',
                'fields' => [
                    [
                        'name' => 'Availability Status',
                        'helpertext' => "Indicate whether the product is currently available or sold out.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Brand',
                        'helpertext' => "Indicate whether the product is currently available or sold out.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Category',
                        'helpertext' => "Categorize the product (e.g.electronics, fashion, home decor,etc.)",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    // [
                    //     'name' => 'Condition',
                    //     'helpertext' => 'Specify whether the product is new,used, or handmade.',
                    //     'type' => 'select',
                    //     'options' => ['New', 'used', 'handmade'],
                    //     'filterable' => true
                    // ],
                    [
                        'name' => 'Delivery/Pick up Options',
                        'helpertext' => "Specify whether the product can be delivered or if it's for pickup only.",
                        'type' => 'select',
                        'options' => ['delivered', 'pickup'],
                        'filterable' => true
                    ],
                    // [
                    //     'name' => 'Description',
                    //     'helpertext' => "Detailed information about the product, including features and specifications.",
                    //     'type' => 'textarea',
                    // ],
                    // [
                    //     'name' => 'Image',
                    //     'helpertext' => "Photos of the product from different angles",
                    //     'type' => 'image',
                    // ],
                    [
                        'name' => 'Payment Methods Accepted',
                        'helpertext' => "Specify the payment options accepted by the seller.",
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Price',
                        'helpertext' => 'The cost of the product.',
                        'type' => 'number',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Quantity Available',
                        'helpertext' => 'Multiple units of the same product.',
                        'type' => 'number',
                    ],
                    [
                        'name' => 'Return Policy',
                        'helpertext' => 'Details on the conditions under which the seller accepts returns',
                        'type' => 'text',
                        'filterable' => true
                    ],
                    [
                        'name' => 'Seller Information',
                        'helpertext' => 'Contact details or information about the seller.',
                        'type' => 'textarea',
                    ],
                    [
                        'name' => 'Shipping Cost',
                        'helpertext' => 'Additional cost for shipping, if applicable.',
                        'type' => 'number',
                    ],

                ]
            ]
        ];
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = $this->getJobTemplateData();
        if (isset($data) && FieldTemplate::count() === 0) {
            foreach ($data as $key => $value) {
                //create default field Template
                $fieldTemplate = FieldTemplate::create([
                    'name' => $value['name'],
                    'default' => true
                ]);
                if (isset($value['fields'])) {
                    foreach ($value['fields'] as $field) {
                        //create default field Template
                        $fieldRecord = Field::create(
                            \Arr::add($field, 'default', true)
                        );
                        //mapping field Template and field
                        FieldTemplateMappings::create(
                            [
                                'field_id' => $fieldRecord->id,
                                'field_template_id' => $fieldTemplate->id,
                                'default' => true
                            ]
                        );
                    }
                }
            }
        }
    }
}
