<?php


namespace App\Service;



use App\Entity\User;

class IsAuthorized
{
    /**
     * Only for the demo solution. So as not to modify the initial themes by the testers of the solution.
     * There are the numbers of objects persisted in the database with the fixed data.
     */
    const NB_INITIAL_IN_DATABASE = [
        'category' => 0,//122,
        'game' => 0,//100,
        'mechanic' => 0,//118,
        'review' => 0,//1619,
        'user' => 0 //36
    ];

    /**
     * Only for the demo solution.So as not to modify the initial themes by the testers of the solution.
     * @param int $id
     * @param string $className
     * @return array|string[]
     */
    public static function ToModifyEntity(int $id, string $className)
    {

        // dd([$className=>IsAuthorized::NB_INITIAL_IN_DATABASE[strtolower($className)]]);
        if ($id <= IsAuthorized::NB_INITIAL_IN_DATABASE[strtolower($className)]) {
            return [
                'type' => 'warning',
                'message' => '403 Access forbidden: only the author of this solution who can delete or modify this entity <br /> Create a new entity to test this function.'
            ];
        }
        return [];
    }

//    /**
//     * To verify user roles before Update or delete any user.
//     * @param User $adminUser
//     * @param User $profileUser
//     * @return bool
//     */

//    private function ToModifyUser(User $adminUser, User $profileUser)
//    {
//        if (
//        !(
//            (
//                !in_array('ROLE_SUPER_ADMIN', $adminUser->getRoles()) &&
//                in_array('ROLE_ADMIN', $adminUser->getRoles()) &&
//                !in_array('ROLE_SUPER_ADMIN', $profileUser->getRoles())
//            )
//            || in_array('ROLE_SUPER_ADMIN', $adminUser->getRoles())
//        )
//        ) {
//            $this->addFlash(
//                'error',
//                '401 Access unauthorized : You don\'t authorized to perform this operation.'
//            );
//
//            return false;
//        }
//        return true;
//    }


}
