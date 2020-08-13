<?php

namespace VkAppSign;

class FakeUser {

  public $args;

  /**
   * Faker constructor.
   */
  public function __construct() {
    $this->args = self::getDefaultArgs();
  }

  public function setArg(string $name, string $value): self {
    $this->args[$name] = $value;
    return $this;
  }

  public function setAppId(int $id) {
    $this->args['vk_app_id'] = $id;
    return $this;
  }

  public function setUserId(int $id) {
    $this->args['vk_user_id'] = $id;
    return $this;
  }

  /**
   * @param int    $groupId
   * @param string $role "none"|"member"|"moder"|"editor"|"admin"
   * @return FakeUser
   */
  public function setGroupId(int $groupId, string $role = 'member') {
    $this->args['vk_group_id'] = $groupId;
    $this->args['vk_viewer_group_role'] = $role;
    return $this;
  }

  public function sign(string $secret): array {
    $this->args = self::signArgs($this->args, $secret);
    return $this->args;
  }

  public function signToUrl(string $secret):string {
    return http_build_query($this->sign($secret));
  }

  public static function getDefaultArgs() {
    return [
      'vk_user_id'                   => 0,
      'vk_app_id'                    => 0,
      'vk_is_app_user'               => 0,
      'vk_are_notifications_enabled' => 0,
      'vk_language'                  => 'ru',
      'vk_ref'                       => 'other',
      'vk_access_token_settings'     => '',
      'vk_platform'                  => "desktop_web",
      'vk_is_favorite'               => 0,
      'sign'                         => '',
    ];
  }

  public static function signArgs(array $args, string $secret): array {
    $args['sign'] = Checker::createVKAppsSign($args, $secret);
    return $args;
  }

  public static function createFakeUser(int $userId, string $secret): array {
    $args               = self::getDefaultArgs();
    $args['vk_user_id'] = $userId;
    $args['sign']       = Checker::createVKAppsSign($args, $secret);
    return $args;
  }

}