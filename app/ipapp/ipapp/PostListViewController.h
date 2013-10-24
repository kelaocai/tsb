//
//  PostListViewController.h
//  ipapp
//
//  Created by kelaocai on 13-10-1.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <UIImageView+WebCache.h>
#import "MBProgressHUD.h"
#import "NSDictionary+Json.h"

@interface PostListViewController : UITableViewController <MBProgressHUDDelegate>
{
    // 当前的页数
    int _current_page;
    //下一页
    int _next_page;
    //总页数
    int _total_page;
    // 每页大小
    int _page_size;

}
@property (strong,nonatomic)NSString *tid;
@property (strong,nonatomic)NSMutableArray *_posts;
@property (strong,nonatomic)NSDictionary *pager;
@property (strong, nonatomic) MBProgressHUD *hud;
@property (retain, nonatomic) IBOutlet UITableView *postTableView;

- (IBAction)moreButton:(id)sender;

@end
