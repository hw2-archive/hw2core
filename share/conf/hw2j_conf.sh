#!/bin/bash

#ignore tables when dbsync
HW2j_RDUMP_OPT="--ignore-table=${HW2_CONF['DB']}.jos_categories "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_content "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_content_rating "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_content_frontpage "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_akeebasubs_subscriptions "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_autotweet_msglog "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_autotweet_queue "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_comprofiler "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_comprofiler_field_values "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_comprofiler_members "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_comprofiler_sessions "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_comprofiler_userreports "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_comprofiler_views "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_contenttemplater "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_core_log_searches "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_core_log_searches "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_terms0 "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_terms1 "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_terms2 "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_terms3 "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_terms4 "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_terms5 "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_terms6 "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_terms7 "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_terms8 "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_terms9 "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_termsa "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_termsb "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_termsc "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_termsd "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_termse "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_termsf "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_taxonomy "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_links_taxonomy_map "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_terms "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_tokens "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_finder_tokens_aggregate "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_hw2_userfields "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_jcomments "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_jcomments_blacklist "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_jcomments_objects "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_jcomments_reports "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_jcomments_subscriptions "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_jcomments_votes "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_jfusion_users "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_jfusion_users_plugin "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_joomgallery "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_joomgallery_catg "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_joomgallery_comments "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_joomgallery_orphans "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_joomgallery_nameshields "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_joomgallery_users "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_joomgallery_votes "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_aliases "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_announcement "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_attachments "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_categories "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_keywords "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_keywords_map "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_messages "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_messages_text "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_polls "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_polls_options "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_polls_users "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_sessions "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_thankyou "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_topics "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_user_read "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_user_topics "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_users "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_kunena_users_banned "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_menu "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_menu_types "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_myrssreader_cache "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_newsfeeds "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_phocamenu_item "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_redirect_links "
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_weblinks"

#user tables
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_session"
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_user_notes"
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_usergroup_map"
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_usergroups"
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.jos_users"

#hw2 tables
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.hw2_content"
HW2j_RDUMP_OPT=$HW2j_RDUMP_OPT"--ignore-table=${HW2_CONF['DB']}.hw2_class_index"


hw2_setConf "RDUMP_OPT" "$HW2j_RDUMP_OPT"

#
# SYMBOLIC LINKS
#

PLATFORM_FILTERS=( 
    # manually linked
    'index.php'
    'index2.php'
    'administrator'$DS'index.php'
    'administrator'$DS'index2.php'
    'administrator'$DS'index3.php'
    # normal filters
    'configuration.php'
    'configuration.php.dist'
    'cache'
    'templates'
    'tmp'
    'logs'
    'images'
    'administrator'$DS'images'
    'administrator'$DS'cache'
)

PLATFORM_INCLUDES=( 
    `hw2_struct 'templates'$DS'system'`
    `hw2_struct 'templates'$DS'hw2_system'`
    `hw2_struct 'index.php' 1`
    `hw2_struct 'index2.php' 1`
    `hw2_struct 'administrator'$DS'index.php' 1`
    `hw2_struct 'administrator'$DS'index2.php' 1`
    `hw2_struct 'administrator'$DS'index3.php' 1`
)


#
#
# [START] version based defines
#
#
FILTERS_1_5=( 
    'components'$DS'com_joomgallery'$DS'assets'$DS'css'$DS'joom_settings.css'
    'components'$DS'com_joomgallery'$DS'ftp_upload'
)

INCLUDES_1_5=( 
)

FILTERS_LATEST=( 
)

INCLUDES_LATEST=( 
)

if [ "${HW2_CONF['VERSION']}" == "1_5" ]; then
    hw2_setConf "CS_TRUNKPATH" '..'$DS'..'$DS'hw2platform1_5'$DS'trunk'$DS
    VERSION_FILTERS=(${FILTERS_1_5[@]})
    VERSION_INCLUDES=(${INCLUDES_1_5[@]})
elif [ "${HW2_CONF['VERSION']}" == "latest" ]; then
    hw2_setConf "CS_TRUNKPATH" '..'$DS'..'$DS'hw2platform_latest'$DS'trunk'$DS
    VERSION_FILTERS=(${FILTERS_LATEST[@]})
    VERSION_INCLUDES=(${INCLUDES_LATEST[@]})
else
    read -p "wrong or not defined version value"
    exit #if no version file found, then return
fi

#
# [END] version based defines
#

FILTERS+=(${VERSION_FILTERS[@]} ${PLATFORM_FILTERS[@]})
INCLUDES+=(${VERSION_INCLUDES[@]} ${PLATFORM_INCLUDES[@]})


HW2_LINKS+=(
    `hw2_struct 1 ".."$DS"images"$DS"shared_images" ${HW2_CONF['CS_HW2PATH']}"share"$DS"media"$DS"images"`
)

#if [ ${HW2_CONF['WORKSPACE']} == "local" ]; then
#    LINKS+=(`hw2_struct 0 "configuration.php" "hw2"$DS"local"$DS"conf"$DS"user_conf"$DS"configuration_local.php"`)
#else
#    LINKS+=(`hw2_struct 0 "configuration.php" "hw2"$DS"local"$DS"conf"$DS"user_conf"$DS"configuration_remote.php"`)
#fi; 


