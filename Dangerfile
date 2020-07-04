has_die_statement = `grep -r 'die(' src/ `.length > 0
has_exit_statement = `grep -r 'exit(' src/ `.length > 0
has_var_dump_statement = `grep -r 'var_dump(' src/ `.length > 0
has_console_log_statement = `grep -r 'console.log' src/ app/ `.length > 0

todoist.print_todos_table

#unless /^(([A-Z])+(-([0-9])+)?|MISC)(:|\s).+$/.match?(github.pr_title)
#  warn("Your PR title format should contain the JIRA ticket reference (`TCP-9999 Fix something`) or be labelled as miscellaneous (`MISC Changed something`)")
#end

if github.pr_labels.empty?
  auto_label.set(github.pr_json["number"], "wip", "#0052cc")
end

if git.added_files.grep(/.*\.patch$/).length > 0 && !git.modified_files.include?("composer.patches.json")
  fail("You added a new patch file but looks like you forgot to update composer.patches.json?")
end

warn("This PR is big, please consider splitting it up next time") if git.lines_of_code > 500

# Don't allow debug statements
fail("die statement left in PHP code") if has_die_statement
fail("exit statement left in PHP code") if has_exit_statement
fail("var_dump statement left in PHP code") if has_var_dump_statement
fail("console.log left in JS code") if has_console_log_statement

random_lgtm = "https://media.giphy.com/media/%s/giphy.gif" % %w(7TtvTUMm9mp20 YoB1eEFB6FZ1m aLdiZJmmx4OVW 4Z3DdOZRTcXPa).sample
lgtm.check_lgtm image_url: random_lgtm, https_image_only: true
