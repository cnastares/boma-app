<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PagesSeeder extends Seeder
{
    public function run()
    {
        $pages = [
            [
                'title' => 'About Us',
                'content' => "AdFox is a dynamic marketplace where users can discover, interact, and finalize purchases seamlessly. We've created a space where buyers meet sellers, and remarkable deals take place every day. Our platform is not just a marketplace but a community of avid buyers and sellers, a network of individuals passionate about trading goods and services.\n\n### Our Mission\nOur mission at AdFox is to provide a straightforward and secure platform for transactional activities, while fostering a community of respectful and engaged users.\n\n### Our Vision\nTo be the go-to platform for buying and selling, renowned for our user-friendly interface, secure transactions, and a vibrant community of users.\n\n### Our Values\n- **Integrity**: Conducting ourselves with high integrity and transparency.\n- **Security**: Ensuring transactions are secure and confidential.\n- **Community**: Building a supportive and respectful network of buyers and sellers.\n- **Innovation**: Constantly improving and innovating our platform for enhanced user experience.",
                'seo_title' => 'About Us',
                'seo_description' => 'Learn more about AdFox.',
                'slug' => 'about-us',
            ],
            [
                'title' => 'Careers',
                'content' => "## Join The AdFox Team\n\n### Why AdFox?\n- **Community Engagement**: As a community-centric platform, engage with a network of active buyers and sellers.\n- **Innovative Culture**: Dive into a culture of innovation and creative problem-solving.\n- **Career Growth**: Opportunities for personal and professional development.\n- **Collaborative Environment**: Work in an environment where collaboration is the cornerstone.\n\n### Current Openings\n- **UI/UX Designer**: Craft engaging and intuitive user interfaces.\n- **Customer Support Specialist**: Be the front-line support for our user community.\n- **Backend Developer**: Work on the robust and scalable backend systems that power AdFox.\n\n### How To Apply\nSend your resume, portfolio (if applicable), and a cover letter expressing your interest and relevant experience to careers@adfox.com with the position title in the subject line.",
                'seo_title' => 'Careers',
                'seo_description' => 'Explore career opportunities at AdFox.',
                'slug' => 'careers',
            ],
            [
                'title' => 'Terms & Conditions',
                'content' => "AdFox provides a platform for buying and selling goods and services. By using AdFox, you agree to the following terms and conditions:\n\n- **User Responsibility**: Users are responsible for the accuracy of the listings and information they provide.\n- **Transaction Integrity**: Users agree to conduct transactions honestly and in good faith.\n- **Privacy Respect**: Adherence to the privacy terms outlined in our Privacy Policy.\n- **Intellectual Property**: Respect for the intellectual property of others.\n\nFor a full list of terms and conditions, please visit our detailed .",
                'seo_title' => 'Terms & Conditions',
                'seo_description' => 'Read the terms and conditions of AdFox.',
                'slug' => 'terms-conditions',
            ],
            [
                'title' => 'Privacy Policy',
                'content' => "At AdFox, we respect the privacy of our users. Our privacy policy outlines:\n\n- **Information Collection**: Details about the types of information we collect.\n- **Information Use**: How we use the collected information.\n- **Information Sharing**: Under what circumstances we might share information.\n- **User Choices**: Options available to users regarding their data.\n\nFor detailed privacy practices, please refer to our complete .",
                'seo_title' => 'Privacy Policy',
                'seo_description' => 'Read the privacy policy of AdFox.',
                'slug' => 'privacy-policy',
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}
