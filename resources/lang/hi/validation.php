<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute को स्वीकार करना आवश्यक है।',
    'accepted_if' => ':attribute को स्वीकार करना आवश्यक है जब :other :value हो।',
    'active_url' => ':attribute एक मान्य URL नहीं है।',
    'after' => ':attribute की तिथि :date के बाद की होनी चाहिए।',
    'after_or_equal' => ':attribute की तिथि :date के बाद या उसके बराबर होनी चाहिए।',
    'alpha' => ':attribute में केवल अक्षर होने चाहिए।',
    'alpha_dash' => ':attribute में केवल अक्षर, संख्या, डैश और अंडरस्कोर हो सकते हैं।',
    'alpha_num' => ':attribute में केवल अक्षर और संख्याएँ हो सकती हैं।',
    'array' => ':attribute एक array होना चाहिए।',
    'before' => ':attribute की तिथि :date से पहले की होनी चाहिए।',
    'before_or_equal' => ':attribute की तिथि :date से पहले या उसके बराबर होनी चाहिए।',
    'between' => [
        'numeric' => ':attribute :min और :max के बीच होना चाहिए।',
        'file' => ':attribute :min और :max किलोबाइट के बीच होना चाहिए।',
        'string' => ':attribute :min और :max अक्षरों के बीच होना चाहिए।',
        'array' => ':attribute में :min और :max आइटम होने चाहिए।',
    ],
    'boolean' => ':attribute फ़ील्ड true या false होना चाहिए।',
    'confirmed' => ':attribute की पुष्टि मेल नहीं खाती।',
    'current_password' => 'पासवर्ड गलत है।',
    'date' => ':attribute एक मान्य तिथि नहीं है।',
    'date_equals' => ':attribute की तिथि :date के बराबर होनी चाहिए।',
    'date_format' => ':attribute का फ़ॉर्मेट :format से मेल नहीं खाता।',
    'declined' => ':attribute को अस्वीकार किया जाना चाहिए।',
    'declined_if' => ':attribute को अस्वीकार किया जाना चाहिए जब :other :value हो।',
    'different' => ':attribute और :other अलग होने चाहिए।',
    'digits' => ':attribute :digits अंकों का होना चाहिए।',
    'digits_between' => ':attribute :min और :max अंकों के बीच होना चाहिए।',
    'dimensions' => ':attribute की इमेज डाइमेंशन अमान्य है।',
    'distinct' => ':attribute फ़ील्ड में डुप्लिकेट मान है।',
    'email' => ':attribute एक मान्य ईमेल पता होना चाहिए।',
    'ends_with' => ':attribute को निम्न में से किसी एक से समाप्त होना चाहिए: :values।',
    'enum' => 'चयनित :attribute अमान्य है।',
    'exists' => 'चयनित :attribute अमान्य है।',
    'file' => ':attribute एक फ़ाइल होना चाहिए।',
    'filled' => ':attribute फ़ील्ड में मान होना चाहिए।',
    'gt' => [
        'numeric' => ':attribute :value से बड़ा होना चाहिए।',
        'file' => ':attribute :value किलोबाइट से बड़ा होना चाहिए।',
        'string' => ':attribute :value अक्षरों से बड़ा होना चाहिए।',
        'array' => ':attribute में :value से अधिक आइटम होने चाहिए।',
    ],
    'gte' => [
        'numeric' => ':attribute :value के बराबर या उससे बड़ा होना चाहिए।',
        'file' => ':attribute :value किलोबाइट के बराबर या उससे बड़ा होना चाहिए।',
        'string' => ':attribute :value अक्षरों के बराबर या उससे बड़ा होना चाहिए।',
        'array' => ':attribute में :value या उससे अधिक आइटम होने चाहिए।',
    ],
    'image' => ':attribute एक इमेज होना चाहिए।',
    'in' => 'चयनित :attribute अमान्य है।',
    'in_array' => ':attribute फ़ील्ड :other में मौजूद नहीं है।',
    'integer' => ':attribute एक पूर्ण संख्या होनी चाहिए।',
    'ip' => ':attribute एक मान्य IP पता होना चाहिए।',
    'ipv4' => ':attribute एक मान्य IPv4 पता होना चाहिए।',
    'ipv6' => ':attribute एक मान्य IPv6 पता होना चाहिए।',
    'json' => ':attribute एक मान्य JSON स्ट्रिंग होनी चाहिए।',
    'lt' => [
        'numeric' => ':attribute :value से कम होना चाहिए।',
        'file' => ':attribute :value किलोबाइट से कम होना चाहिए।',
        'string' => ':attribute :value अक्षरों से कम होना चाहिए।',
        'array' => ':attribute में :value से कम आइटम होने चाहिए।',
    ],
    'lte' => [
        'numeric' => ':attribute :value से कम या उसके बराबर होना चाहिए।',
        'file' => ':attribute :value किलोबाइट से कम या उसके बराबर होना चाहिए।',
        'string' => ':attribute :value अक्षरों से कम या उसके बराबर होना चाहिए।',
        'array' => ':attribute में :value से अधिक आइटम नहीं होने चाहिए।',
    ],
    'mac_address' => ':attribute एक मान्य MAC पता होना चाहिए।',
    'max' => [
        'numeric' => ':attribute :max से अधिक नहीं होना चाहिए।',
        'file' => ':attribute :max किलोबाइट से अधिक नहीं होना चाहिए।',
        'string' => ':attribute :max अक्षरों से अधिक नहीं होना चाहिए।',
        'array' => ':attribute में :max से अधिक आइटम नहीं होने चाहिए।',
    ],
    'mimes' => ':attribute फ़ाइल का प्रकार होना चाहिए: :values।',
    'mimetypes' => ':attribute फ़ाइल का प्रकार होना चाहिए: :values।',
    'min' => [
        'numeric' => ':attribute कम से कम :min होना चाहिए।',
        'file' => ':attribute कम से कम :min किलोबाइट होना चाहिए।',
        'string' => ':attribute कम से कम :min अक्षरों का होना चाहिए।',
        'array' => ':attribute में कम से कम :min आइटम होने चाहिए।',
    ],
    'multiple_of' => ':attribute :value का गुणज होना चाहिए।',
    'not_in' => 'चयनित :attribute अमान्य है।',
    'not_regex' => ':attribute का फ़ॉर्मेट अमान्य है।',
    'numeric' => ':attribute एक संख्या होनी चाहिए।',
    'password' => 'पासवर्ड गलत है।',
    'present' => ':attribute फ़ील्ड मौजूद होना चाहिए।',
    'prohibited' => ':attribute फ़ील्ड प्रतिबंधित है।',
    'prohibited_if' => ':attribute फ़ील्ड प्रतिबंधित है जब :other :value हो।',
    'prohibited_unless' => ':attribute फ़ील्ड प्रतिबंधित है जब तक :other :values में न हो।',
    'prohibits' => ':attribute फ़ील्ड :other को मौजूद होने से रोकता है।',
    'regex' => ':attribute का फ़ॉर्मेट अमान्य है।',
    'required' => ':attribute फ़ील्ड आवश्यक है।',
    'required_array_keys' => ':attribute फ़ील्ड में निम्नलिखित के लिए प्रविष्टियाँ होनी चाहिए: :values।',
    'required_if' => ':attribute फ़ील्ड आवश्यक है जब :other :value हो।',
    'required_unless' => ':attribute फ़ील्ड आवश्यक है जब तक :other :values में न हो।',
    'required_with' => ':attribute फ़ील्ड आवश्यक है जब :values मौजूद हो।',
    'required_with_all' => ':attribute फ़ील्ड आवश्यक है जब :values सभी मौजूद हों।',
    'required_without' => ':attribute फ़ील्ड आवश्यक है जब :values मौजूद न हो।',
    'required_without_all' => ':attribute फ़ील्ड आवश्यक है जब :values में से कोई भी मौजूद न हो।',
    'same' => ':attribute और :other समान होने चाहिए।',
    'size' => [
        'numeric' => ':attribute :size होना चाहिए।',
        'file' => ':attribute :size किलोबाइट होना चाहिए।',
        'string' => ':attribute :size अक्षरों का होना चाहिए।',
        'array' => ':attribute में :size आइटम होने चाहिए।',
    ],
    'starts_with' => ':attribute को निम्न में से किसी एक से शुरू होना चाहिए: :values।',
    'string' => ':attribute एक स्ट्रिंग होना चाहिए।',
    'timezone' => ':attribute एक मान्य टाइमज़ोन होना चाहिए।',
    'unique' => ':attribute पहले से उपयोग में है।',
    'uploaded' => ':attribute अपलोड नहीं हो सका।',
    'url' => ':attribute एक मान्य URL होना चाहिए।',
    'uuid' => ':attribute एक मान्य UUID होना चाहिए।',


    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
