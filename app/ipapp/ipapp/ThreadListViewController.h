//
//  PostListViewController.h
//  ipapp
//
//  Created by kelaocai on 13-9-29.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "AFNetworking.h"
#import "ThreadListTableCell.h"
#import "MBProgressHUD.h"
@interface ThreadListViewController : UITableViewController <MBProgressHUDDelegate>{
    //当前的页数
    int _current_page;
    //下一页
    int _next_page;
    //总页数
    int _total_page;
    //每页大小
    int _page_size;
}
@property(strong,nonatomic)NSMutableArray *threads;
@property (strong,nonatomic)NSString *fid;
@property (strong, nonatomic) MBProgressHUD *hud;
@property (retain, nonatomic) IBOutlet UITableView *threadTableView;

@end
