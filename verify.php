<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = htmlspecialchars($_POST['username']);
        $_SESSION['username'] = $username; 
        echo "<script>console.log('Session username set: " . $username . "');</script>";
        $apiUrl = "https://users.roblox.com/v1/usernames/users";
        $postData = json_encode(["usernames" => [$username], "excludeBannedUsers" => true]);
        $options = [
            "http" => [
                "header" => "Content-Type: application/json\r\n",
                "method" => "POST",
                "content" => $postData,
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($apiUrl, false, $context);

        if ($response === FALSE) {
            echo "<script>Swal.fire('Error', 'Failed to fetch user data','error');</script>";
            exit;
        }
        $userData = json_decode($response, true);
        if (isset($userData['data'][0]['id'])) {
            $userId = $userData['data'][0]['id'];
            $randomText = generateRandomText();

            echo "<script>
                const randomText = '{$randomText}';
                const userId = '{$userId}';

                async function verifyDescription() {
                    try {
                        const userCheckResponse = await fetch(`https://users.roproxy.com/v1/users/${userId}`);
                        if (!userCheckResponse.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const userCheckData = await userCheckResponse.json();
                        if (userCheckData.description && userCheckData.description.includes(randomText)) {
                            Swal.fire({
                                title: 'Success',
                                text: 'Verification successful. Redirecting...',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                willClose: () => {
                                window.location.href = '/dashboard.php';  <---- CHANGE THIS TO ANYTHING YOU WANT!
                                }
                            });
                        } else {
                            Swal.fire('Error', 'Text not found in description.', 'error');
                        }
                    } catch (error) {
                        console.error('Failed to verify the description:', error);
                        Swal.fire('Error', 'Failed to verify the description', 'error');
                    }
                }

                Swal.fire({
                    title: 'Please add the following text to your description:',
                    text: `${randomText}`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Done',
                    willClose: verifyDescription
                });
            </script>";
        } else {
            echo "<script>Swal.fire('Error', 'User not found', 'error');</script>";
        }
    }
    function generateRandomText() {
       $words = ['fun', 'alpha', 'lion', 'fun', 'very', 'exciting', 'adventure', 'brilliant', 'dynamic', 'energetic', 'vibrant', 'courageous', 'innovative', 'passionate', 'enthusiastic', 'engineer'];  /* you can change these to anything you want */
        shuffle($words);
        return implode(' ', $words);
    }
    ?>
