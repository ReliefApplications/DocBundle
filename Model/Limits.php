<?php
namespace RA\DocBundle\Model;

class Limits
{
    const limit_nb_rosters_managed = 7; //Number of rosters a manager can manage
    const limit_nb_roster_Members = 50; //Number of users in a Roster
    const limit_nb_roster_active_member = 10; //Number of rosters where a user is a member
    const limit_user_gallery_size = 52428800; //Size in bytes now 25 Mo
    const max_document_size = "4M";

}
